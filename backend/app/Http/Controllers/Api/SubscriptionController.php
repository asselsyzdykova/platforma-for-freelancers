<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class SubscriptionController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        if ($user->role !== 'freelancer') {
            return response()->json(['error' => 'Only freelancers can subscribe'], 403);
        }

        $validated = $request->validate([
            'plan' => ['nullable', 'string'],
            'price_id' => ['nullable', 'string'],
        ]);

        $priceId = $validated['price_id'] ?? null;
        if (!$priceId && !empty($validated['plan'])) {
            $plan = strtolower($validated['plan']);
            $priceId = match ($plan) {
                'pro' => env('STRIPE_PRICE_PRO'),
                'premium' => env('STRIPE_PRICE_PREMIUM'),
                default => null,
            };
        }

        if (!$priceId) {
            return response()->json(['error' => 'Subscription price is not configured'], 422);
        }

        $stripeSecret = env('STRIPE_SECRET');
        if (!$stripeSecret) {
            return response()->json(['error' => 'Stripe secret key is not configured'], 500);
        }

        $frontendUrl = env('FRONTEND_URL');
        if (!$frontendUrl) {
            return response()->json(['error' => 'Frontend URL is not configured'], 500);
        }

        Stripe::setApiKey($stripeSecret);

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'subscription',
                'customer_email' => $user->email,
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'price_id' => $priceId,
                ],
                'success_url' => rtrim($frontendUrl, '/') . '/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => rtrim($frontendUrl, '/') . '/cancel',
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'id' => $session->id,
            'url' => $session->url,
        ]);
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch(\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $user = User::where('email', $session->customer_email)->first();
                if ($user) {
                    $priceId = $session->metadata->price_id ?? null;

                    Subscription::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'stripe_customer_id' => $session->customer,
                            'stripe_subscription_id' => $session->subscription,
                            'plan' => $priceId,
                            'status' => 'active',
                        ]
                    );
                }
                break;

            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
                if ($subscription) {
                    $subscription->status = 'past_due';
                    $subscription->save();
                }
                break;

            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                $localSub = Subscription::where('stripe_subscription_id', $subscription->id)->first();
                if ($localSub) {
                    $localSub->status = 'canceled';
                    $localSub->save();
                }
                break;
        }

        return response('Webhook handled', 200);
    }
}
