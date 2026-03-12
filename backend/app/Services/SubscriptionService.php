<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Milestone;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Carbon\Carbon;

class SubscriptionService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function handleSubscriptionSync(User $user, string $stripeSubscriptionId, ?string $priceId)
    {
        Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);
            
        return Subscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'provider' => 'stripe',
                'provider_id' => $stripeSubscriptionId,
            ],
            [
                'plan' => $priceId,
                'status' => 'active',
                'start_date' => now(),
                'end_date' => null,
            ]
        );
    }

    public function cancelStripeSubscription(Subscription $subscription)
    {
        $stripeResponse = $this->stripe->subscriptions->update($subscription->provider_id, [
            'cancel_at_period_end' => true
        ]);

        $timestamp = $stripeResponse->current_period_end ?? time();
        $endDate = Carbon::createFromTimestamp($timestamp);

        $subscription->update([
            'status' => 'canceled',
            'end_date' => $endDate,
        ]);

        return $endDate;
    }
    public function processMilestonePayment(int $milestoneId)
    {
        $milestone = Milestone::find($milestoneId);
        if ($milestone) {
            $milestone->update(['payment_status' => 'paid', 'paid_at' => now()]);
            Log::info("Milestone {$milestoneId} marked as paid.");
            return true;
        }
        return false;
    }
}
