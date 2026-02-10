<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class SubscriptionController extends Controller
{
    private function resolvePaypalUser(array $resource): ?User
    {
        $customId = $resource['custom_id'] ?? null;
        if (!empty($customId)) {
            $user = User::find($customId);
            if ($user) {
                return $user;
            }
        }

        $email = $resource['subscriber']['email_address'] ?? null;
        if (!empty($email)) {
            return User::where('email', $email)->first();
        }

        return null;
    }

    private function verifyPaypalWebhook(Request $request, array $payload): bool
    {
        $webhookId = env('PAYPAL_WEBHOOK_ID');
        if (!$webhookId) {
            Log::warning('PayPal webhook verification skipped: missing webhook id');
            return false;
        }

        $authAlgo = $request->header('PayPal-Auth-Algo');
        $certUrl = $request->header('PayPal-Cert-Url');
        $transmissionId = $request->header('PayPal-Transmission-Id');
        $transmissionSig = $request->header('PayPal-Transmission-Sig');
        $transmissionTime = $request->header('PayPal-Transmission-Time');

        if (!$authAlgo || !$certUrl || !$transmissionId || !$transmissionSig || !$transmissionTime) {
            Log::warning('PayPal webhook verification failed: missing headers', [
                'auth_algo' => $authAlgo,
                'cert_url' => $certUrl,
                'transmission_id' => $transmissionId,
                'transmission_sig' => $transmissionSig,
                'transmission_time' => $transmissionTime,
            ]);
            return false;
        }

        $baseUrl = rtrim(env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com'), '/');

        try {
            $token = $this->getPaypalAccessToken();

            $client = new Client([
                'base_uri' => $baseUrl,
                'timeout' => 20,
            ]);

            $response = $client->post('/v1/notifications/verify-webhook-signature', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'auth_algo' => $authAlgo,
                    'cert_url' => $certUrl,
                    'transmission_id' => $transmissionId,
                    'transmission_sig' => $transmissionSig,
                    'transmission_time' => $transmissionTime,
                    'webhook_id' => $webhookId,
                    'webhook_event' => $payload,
                ],
            ]);
        } catch (GuzzleException $e) {
            Log::error('PayPal webhook verification failed', [
                'message' => $e->getMessage(),
            ]);
            return false;
        } catch (\RuntimeException $e) {
            Log::error('PayPal webhook verification failed', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }

        $result = json_decode((string) $response->getBody(), true);
        $status = strtoupper((string) ($result['verification_status'] ?? ''));

        return $status === 'SUCCESS';
    }

    private function getPaypalAccessToken(): string
    {
        $clientId = env('PAYPAL_CLIENT_ID');
        $clientSecret = env('PAYPAL_CLIENT_SECRET');
        $baseUrl = rtrim(env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com'), '/');

        if (!$clientId || !$clientSecret) {
            throw new \RuntimeException('PayPal credentials are not configured');
        }

        $client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => 20,
        ]);

        try {
            $response = $client->post('/v1/oauth2/token', [
                'auth' => [$clientId, $clientSecret],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('PayPal auth failed: ' . $e->getMessage());
        }

        $payload = json_decode((string) $response->getBody(), true);
        if (empty($payload['access_token'])) {
            throw new \RuntimeException('PayPal token missing in response');
        }

        return $payload['access_token'];
    }

    private function getPaypalPlanId(string $plan): ?string
    {
        return match ($plan) {
            'pro' => env('PAYPAL_PLAN_PRO'),
            'premium' => env('PAYPAL_PLAN_PREMIUM'),
            default => null,
        };
    }

    private function mapPaypalPlan(string $planId): ?string
    {
        if ($planId === env('PAYPAL_PLAN_PRO')) {
            return 'pro';
        }
        if ($planId === env('PAYPAL_PLAN_PREMIUM')) {
            return 'premium';
        }
        return null;
    }

    public function cancel(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('created_at')
            ->first();

        if (!$subscription) {
            return response()->json(['error' => 'Active subscription not found'], 404);
        }

        $subscription->status = 'canceled';
        $subscription->end_date = now();
        $subscription->save();

        return response()->json([
            'status' => 'ok',
            'subscription' => $subscription,
        ]);
    }
    public function transactions(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $perPage = (int) $request->get('per_page', 8);
        $perPage = $perPage > 0 ? min($perPage, 50) : 8;

        $paginated = Subscription::where('user_id', $user->id)
            ->latest('created_at')
            ->paginate($perPage);

        $transactions = collect($paginated->items())->map(function ($subscription) use ($user) {
            $plan = $subscription->plan;
            if ($plan === env('STRIPE_PRICE_PRO')) {
                $plan = 'pro';
            } elseif ($plan === env('STRIPE_PRICE_PREMIUM')) {
                $plan = 'premium';
            }

            $label = $plan ? Str::ucfirst((string) $plan) : 'Subscription';

            return [
                'date' => optional($subscription->created_at)->toDateString(),
                'type' => 'Subscription',
                'description' => $label,
                'party' => $user->role ?? 'freelancer',
                'amount' => null,
                'status' => $subscription->status,
                'id' => $subscription->provider_id ?? $subscription->id,
            ];
        });

        return response()->json([
            'data' => $transactions,
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }
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
                'client_reference_id' => (string) $user->id,
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'price_id' => $priceId,
                ],
                'success_url' => rtrim($frontendUrl, '/') . '/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => rtrim($frontendUrl, '/') . '/subscriptions',
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

    public function createPaypalSubscription(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        if ($user->role !== 'freelancer') {
            return response()->json(['error' => 'Only freelancers can subscribe'], 403);
        }

        $validated = $request->validate([
            'plan' => ['required', 'string'],
        ]);

        $plan = strtolower($validated['plan']);
        $planId = $this->getPaypalPlanId($plan);
        if (!$planId) {
            return response()->json(['error' => 'PayPal plan is not configured'], 422);
        }

        $frontendUrl = env('FRONTEND_URL');
        if (!$frontendUrl) {
            return response()->json(['error' => 'Frontend URL is not configured'], 500);
        }

        $baseUrl = rtrim(env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com'), '/');
        $client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => 20,
        ]);

        try {
            $token = $this->getPaypalAccessToken();

            $response = $client->post('/v1/billing/subscriptions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'plan_id' => $planId,
                    'custom_id' => (string) $user->id,
                    'application_context' => [
                        'brand_name' => 'BezRab',
                        'user_action' => 'SUBSCRIBE_NOW',
                        'return_url' => rtrim($frontendUrl, '/') . '/success?provider=paypal',
                        'cancel_url' => rtrim($frontendUrl, '/') . '/subscriptions',
                    ],
                ],
            ]);
        } catch (RequestException $e) {
            $details = '';
            if ($e->getResponse()) {
                $details = (string) $e->getResponse()->getBody();
            }
            Log::error('PayPal create subscription failed', [
                'message' => $e->getMessage(),
                'details' => $details,
                'plan_id' => $planId,
            ]);
            return response()->json([
                'error' => 'PayPal API error',
                'details' => $details,
            ], 422);
        } catch (GuzzleException $e) {
            Log::error('PayPal create subscription failed', [
                'message' => $e->getMessage(),
                'plan_id' => $planId,
            ]);
            return response()->json([
                'error' => 'PayPal API error',
            ], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        $payload = json_decode((string) $response->getBody(), true);
        $approvalLink = collect($payload['links'] ?? [])
            ->firstWhere('rel', 'approve');

        if (empty($approvalLink['href'])) {
            return response()->json(['error' => 'PayPal approval link missing'], 422);
        }

        return response()->json([
            'id' => $payload['id'] ?? null,
            'url' => $approvalLink['href'],
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
                Log::info('Stripe webhook: checkout.session.completed', [
                    'session_id' => $session->id ?? null,
                    'subscription_id' => $session->subscription ?? null,
                    'client_reference_id' => $session->client_reference_id ?? null,
                    'customer_email' => $session->customer_email ?? null,
                ]);
                $user = null;
                if (!empty($session->client_reference_id)) {
                    $user = User::find($session->client_reference_id);
                }
                if (!$user && !empty($session->customer_email)) {
                    $user = User::where('email', $session->customer_email)->first();
                }
                if ($user) {
                    $priceId = $session->metadata->price_id ?? null;

                    Subscription::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'provider' => 'stripe',
                            'provider_id' => $session->subscription,
                        ],
                        [
                            'plan' => $priceId,
                            'status' => 'active',
                            'start_date' => now(),
                            'end_date' => null,
                        ]
                    );
                    Log::info('Stripe webhook: subscription stored', [
                        'user_id' => $user->id,
                        'provider_id' => $session->subscription ?? null,
                        'plan' => $priceId,
                    ]);
                } else {
                    Log::warning('Stripe webhook: user not found for session', [
                        'client_reference_id' => $session->client_reference_id ?? null,
                        'customer_email' => $session->customer_email ?? null,
                    ]);
                }
                break;

            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                Log::info('Stripe webhook: invoice.payment_failed', [
                    'subscription_id' => $invoice->subscription ?? null,
                ]);
                $subscription = Subscription::where('provider', 'stripe')
                    ->where('provider_id', $invoice->subscription)
                    ->first();
                if ($subscription) {
                    $subscription->status = 'expired';
                    $subscription->save();
                }
                break;

            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                Log::info('Stripe webhook: customer.subscription.deleted', [
                    'subscription_id' => $subscription->id ?? null,
                ]);
                $localSub = Subscription::where('provider', 'stripe')
                    ->where('provider_id', $subscription->id)
                    ->first();
                if ($localSub) {
                    $localSub->status = 'canceled';
                    $localSub->end_date = now();
                    $localSub->save();
                }
                break;
        }

        return response('Webhook handled', 200);
    }

    public function handlePaypalWebhook(Request $request)
    {
        $raw = $request->getContent();
        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            return response('Invalid payload', 400);
        }

        if (!$this->verifyPaypalWebhook($request, $payload)) {
            return response('Invalid signature', 400);
        }

        $eventType = strtoupper((string) ($payload['event_type'] ?? ''));
        $resource = $payload['resource'] ?? [];

        $subscriptionId = $resource['id'] ?? null;
        if (!$subscriptionId) {
            Log::warning('PayPal webhook: subscription id missing', [
                'event_type' => $eventType,
            ]);
            return response('Webhook handled', 200);
        }

        $user = $this->resolvePaypalUser($resource);
        if (!$user) {
            Log::warning('PayPal webhook: user not found', [
                'event_type' => $eventType,
                'subscription_id' => $subscriptionId,
                'custom_id' => $resource['custom_id'] ?? null,
                'email' => $resource['subscriber']['email_address'] ?? null,
            ]);
            return response('Webhook handled', 200);
        }

        $planId = $resource['plan_id'] ?? null;
        $plan = $planId ? $this->mapPaypalPlan($planId) : null;

        $paypalStatus = strtoupper((string) ($resource['status'] ?? ''));
        $status = match ($paypalStatus) {
            'ACTIVE' => 'active',
            'CANCELLED' => 'canceled',
            'EXPIRED' => 'expired',
            'SUSPENDED' => 'expired',
            'APPROVAL_PENDING' => 'expired',
            default => 'active',
        };

        if (in_array($eventType, [
            'BILLING.SUBSCRIPTION.CANCELLED',
            'BILLING.SUBSCRIPTION.SUSPENDED',
            'BILLING.SUBSCRIPTION.EXPIRED',
        ], true)) {
            if ($eventType === 'BILLING.SUBSCRIPTION.EXPIRED') {
                $status = 'expired';
            } elseif ($eventType === 'BILLING.SUBSCRIPTION.SUSPENDED') {
                $status = 'expired';
            } else {
                $status = 'canceled';
            }
        }

        $endDate = in_array($status, ['canceled', 'expired'], true) ? now() : null;

        Subscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'provider' => 'paypal',
                'provider_id' => $subscriptionId,
            ],
            [
                'plan' => $plan ?? $planId,
                'status' => $status,
                'start_date' => now(),
                'end_date' => $endDate,
            ]
        );

        Log::info('PayPal webhook: subscription updated', [
            'user_id' => $user->id,
            'provider_id' => $subscriptionId,
            'plan' => $plan ?? $planId,
            'status' => $status,
            'event_type' => $eventType,
        ]);

        return response('Webhook handled', 200);
    }

    public function confirmCheckout(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        $stripeSecret = env('STRIPE_SECRET');
        if (!$stripeSecret) {
            return response()->json(['error' => 'Stripe secret key is not configured'], 500);
        }

        Stripe::setApiKey($stripeSecret);

        try {
            $session = Session::retrieve($validated['session_id']);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        if (!empty($session->client_reference_id) && (string) $user->id !== (string) $session->client_reference_id) {
            return response()->json(['error' => 'Session does not belong to the current user'], 403);
        }

        if (empty($session->subscription)) {
            return response()->json(['error' => 'Subscription not found for this session'], 422);
        }

        $priceId = $session->metadata->price_id ?? null;

        Subscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'provider' => 'stripe',
                'provider_id' => $session->subscription,
            ],
            [
                'plan' => $priceId,
                'status' => 'active',
                'start_date' => now(),
                'end_date' => null,
            ]
        );

        Log::info('Stripe confirm: subscription stored', [
            'user_id' => $user->id,
            'provider_id' => $session->subscription,
            'plan' => $priceId,
        ]);

        return response()->json([
            'status' => 'ok',
            'plan' => $priceId,
        ]);
    }

    public function confirmPaypalSubscription(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'subscription_id' => ['required', 'string'],
        ]);

        $subscriptionId = $validated['subscription_id'];
        $baseUrl = rtrim(env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com'), '/');

        try {
            $token = $this->getPaypalAccessToken();

            $client = new Client([
                'base_uri' => $baseUrl,
                'timeout' => 20,
            ]);

            $response = $client->get('/v1/billing/subscriptions/' . $subscriptionId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
            ]);
        } catch (RequestException $e) {
            $details = '';
            if ($e->getResponse()) {
                $details = (string) $e->getResponse()->getBody();
            }
            Log::error('PayPal confirm subscription failed', [
                'message' => $e->getMessage(),
                'details' => $details,
                'subscription_id' => $subscriptionId,
            ]);
            return response()->json([
                'error' => 'PayPal API error',
                'details' => $details,
            ], 422);
        } catch (GuzzleException $e) {
            Log::error('PayPal confirm subscription failed', [
                'message' => $e->getMessage(),
                'subscription_id' => $subscriptionId,
            ]);
            return response()->json([
                'error' => 'PayPal API error',
            ], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        $payload = json_decode((string) $response->getBody(), true);
        $status = strtoupper((string) ($payload['status'] ?? ''));
        if (!in_array($status, ['ACTIVE', 'APPROVED'], true)) {
            return response()->json(['error' => 'PayPal subscription is not active'], 422);
        }

        $planId = $payload['plan_id'] ?? null;
        $plan = $planId ? $this->mapPaypalPlan($planId) : null;
        if (!$plan) {
            return response()->json(['error' => 'Unknown PayPal plan'], 422);
        }

        Subscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'provider' => 'paypal',
                'provider_id' => $subscriptionId,
            ],
            [
                'plan' => $plan,
                'status' => 'active',
                'start_date' => now(),
                'end_date' => null,
            ]
        );

        Log::info('PayPal confirm: subscription stored', [
            'user_id' => $user->id,
            'provider_id' => $subscriptionId,
            'plan' => $plan,
        ]);

        return response()->json([
            'status' => 'ok',
            'plan' => $plan,
        ]);
    }
}
