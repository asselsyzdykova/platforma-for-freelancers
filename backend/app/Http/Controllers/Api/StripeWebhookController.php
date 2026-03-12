<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Milestone;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;
use App\Services\SubscriptionService;

class StripeWebhookController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }

        $session = $event->data->object;
        $milestoneId = $session->metadata->milestone_id ?? null;
        switch ($event->type) {
            case 'checkout.session.completed':
                if ($milestoneId) {
                    $this->subscriptionService->processMilestonePayment((int)$milestoneId);
                } else {
                    $user = null;
                    if (!empty($session->client_reference_id)) {
                        $user = \App\Models\User::find($session->client_reference_id);
                    }

                    if ($user && !empty($session->subscription)) {
                        $priceId = $session->metadata->price_id ?? null;
                        $this->subscriptionService->handleSubscriptionSync($user, $session->subscription, $priceId);
                    }
                }
                break;

            case 'payment_intent.payment_failed':
            case 'checkout.session.expired':
                $milestone = Milestone::find($milestoneId);
                if ($milestone) {
                    $milestone->update(['payment_status' => 'pending']);
                    Log::warning("Milestone $milestoneId: Payment failed/expired");
                }
                break;
        }

        return response()->json(['status' => 'success']);
    }
}
