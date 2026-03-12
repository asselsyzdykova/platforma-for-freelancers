<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Services\SubscriptionService;
use App\Models\Milestone;
use Illuminate\Pagination\LengthAwarePaginator;

class SubscriptionController extends Controller
{
    protected $subscriptionService;
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
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
                'pro' => config('services.stripe.price_pro'),
                'premium' => config('services.stripe.price_premium'),
                default => null,
            };
        }

        if (!$priceId) {
            return response()->json(['error' => 'Subscription price is not configured'], 422);
        }

        $stripeSecret = config('services.stripe.secret');
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

    public function confirmCheckout(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        $stripeSecret = config('services.stripe.secret');
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

        $this->subscriptionService->handleSubscriptionSync($user, $session->subscription, $priceId);

        return response()->json([
            'status' => 'ok',
            'plan' => $priceId,
        ]);
    }

    public function cancel(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('provider', 'stripe')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json(['error' => 'No active Stripe subscription found'], 404);
        }

        try {
            $endDate = $this->subscriptionService->cancelStripeSubscription($subscription);

            return response()->json([
                'status' => 'ok',
                'message' => 'Subscription will remain active until ' . $endDate->toDateString(),
                'end_date' => $endDate->toDateString()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to cancel subscription: ' . $e->getMessage()], 422);
        }
    }
    public function transactions(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $perPage = (int) $request->input('per_page', 8);
        $perPage = $perPage > 0 ? min($perPage, 50) : 8;

        $subs = Subscription::where('user_id', $user->id)
            ->latest()
            ->take(100)
            ->get();

        $milestones = Milestone::whereHas('project', function ($query) use ($user) {
            $query->where('freelancer_id', $user->id);
        })
            ->with('project.client')
            ->where('payment_status', 'paid')
            ->latest()
            ->take(100)
            ->get();
        $formattedSubs = $subs->map(function ($sub) use ($user) {
            $plan = $sub->plan;
            if ($plan === config('services.stripe.price_pro')) $plan = 'pro';
            elseif ($plan === config('services.stripe.price_premium')) $plan = 'premium';

            return [
                'date' => optional($sub->created_at)->toDateString(),
                'type' => 'Subscription',
                'description' => $plan ? 'Plan: ' . Str::ucfirst($plan) : 'Subscription',
                'party' => 'System',
                'amount' => '-',
                'status' => $sub->status,
                'id' => $sub->provider_id ?? $sub->id,
                'raw_date' => $sub->created_at,
            ];
        });

        $formattedMilestones = $milestones->map(function ($m) {
            return [
                'date' => optional($m->paid_at ?? $m->updated_at)->toDateString(),
                'type' => 'Milestone Payment',
                'description' => $m->title ?? 'Project Milestone',
                'party' => $m->project->client->name ?? 'Client',
                'amount' => $m->amount ? $m->amount . ' EUR' : '-',
                'status' => 'paid',
                'id' => 'MS-' . $m->id,
                'raw_date' => $m->paid_at ?? $m->updated_at,
            ];
        });

        $allTransactions = $formattedSubs->concat($formattedMilestones)
            ->sortByDesc('raw_date')
            ->values();

        $page = $request->input('page', 1);
        $paginated = new LengthAwarePaginator(
            $allTransactions->forPage($page, $perPage),
            $allTransactions->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json([
            'data' => $paginated->values(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }
}
