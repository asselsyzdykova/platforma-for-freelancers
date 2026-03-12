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

        $plan = 'free';
        $status = 'none';
        $subscription = Subscription::where('user_id', $user->id)
            ->latest('created_at')
            ->first();
        if ($subscription) {
            $status = $subscription->status;
            $rawPlan = $subscription->plan;

            $proPrice = config('services.stripe.price_pro');
            $premiumPrice = config('services.stripe.price_premium');

            if ($rawPlan === $proPrice || $rawPlan === 'pro') {
                $plan = 'pro';
            } elseif ($rawPlan === $premiumPrice || $rawPlan === 'premium') {
                $plan = 'premium';
            } else {
                $plan = 'free';
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
            'subscription_status' => $status,
            'created_at' => $user->created_at,
        ]);
    }

    public function show($id)
    {
        $user = \App\Models\User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? 'freelancer',
            'university' => $user->university,
            'study_year' => $user->study_year,
            'created_at' => $user->created_at,
        ]);
    }
}
