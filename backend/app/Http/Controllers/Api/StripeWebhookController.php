<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Milestone;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
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
        if (!$milestoneId) {
            return response()->json(['status' => 'ignored_no_metadata']);
        }
        $milestone = Milestone::find($milestoneId);

        if ($milestone) {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $milestone->update([
                        'payment_status' => 'paid',
                        'paid_at' => now()
                    ]);
                    Log::info("Milestone $milestoneId: Payment successful");
                    break;

                case 'payment_intent.payment_failed':
                case 'checkout.session.expired':
                    $milestone->update([
                        'payment_status' => 'pending'
                    ]);
                    Log::warning("Milestone $milestoneId: Payment failed or expired ($event->type)");
                    break;
            }
        }

        return response()->json(['status' => 'success']);
    }
}
