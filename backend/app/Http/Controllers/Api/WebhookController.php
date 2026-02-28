<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Stripe;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );

            switch ($event->type) {
                case 'customer.subscription.created':
                    $subscription = $event->data->object;
                    Log::info('Subscription created: ' . $subscription->id);
                    break;

                case 'customer.subscription.updated':
                    $subscription = $event->data->object;
                    Log::info('Subscription updated: ' . $subscription->id);
                    break;

                case 'customer.subscription.deleted':
                    $subscription = $event->data->object;
                    Log::info('Subscription deleted: ' . $subscription->id);
                    break;

                case 'invoice.paid':
                    $invoice = $event->data->object;
                    Log::info('Invoice paid: ' . $invoice->id);
                    break;

                case 'invoice.payment_failed':
                    $invoice = $event->data->object;
                    Log::warning('Invoice payment failed: ' . $invoice->id);
                    break;
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
