<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;

class UserController extends Controller
{
    public function me(Request $request)

    {
        $user = $request->user();

        $plan = null;
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('created_at')
            ->first();

        if ($subscription) {
            $plan = $subscription->plan;
            if ($plan === env('STRIPE_PRICE_PRO')) {
                $plan = 'pro';
            } elseif ($plan === env('STRIPE_PRICE_PREMIUM')) {
                $plan = 'premium';
            } elseif ($plan === 'free') {
                $plan = 'free';
            } else {
                $plan = 'pro';
            }
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'university' => $user->university,
            'study_year' => $user->study_year,
            'role' => $user->role ?? 'freelancer',
            'plan' => $plan,
            'created_at' => $user->created_at,
        ]);
    }

}
