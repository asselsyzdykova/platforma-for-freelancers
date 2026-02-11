<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Project;

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
            } elseif ($plan === 'premium') {
                $plan = 'premium';
            } elseif ($plan === 'pro') {
                $plan = 'pro';
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

    public function adminStats(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        if (($user->role ?? '') !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $now = Carbon::now();
        $currentStart = $now->copy()->startOfMonth();
        $previousStart = $now->copy()->subMonth()->startOfMonth();
        $previousEnd = $currentStart->copy()->subSecond();

        $currentCount = User::where('created_at', '>=', $currentStart)->count();
        $previousCount = User::whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $userGrowth = 0;
        if ($previousCount > 0) {
            $userGrowth = (int) round((($currentCount - $previousCount) / $previousCount) * 100);
        } elseif ($currentCount > 0) {
            $userGrowth = 100;
        }

        $currentCountFreelancer = User::where('role', 'freelancer')->
        where('created_at', '>=', $currentStart)->count();

        $previousCountFreelancer = User::where('role', 'freelancer')->
        whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $freelancerGrowth = 0;
        if ($previousCountFreelancer > 0){
            $freelancerGrowth = (int) round((($currentCountFreelancer - $previousCountFreelancer) /
            $previousCountFreelancer) * 100);
        } elseif ($currentCountFreelancer > 0) {
            $freelancerGrowth = 100;
        }

        //active projects
        $currentCountProject = Project::where('created_at', '>=', $currentStart)->count();
        $previousCountProject = Project::whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $projectGrowth = 0;
        if ($previousCountProject > 0) {
            $projectGrowth = (int) round((($currentCountProject - $previousCountProject) / $previousCountProject) * 100);
        } elseif ($currentCountProject > 0) {
            $projectGrowth = 100;
        }

        return response()->json([
            'total_freelancers' => (int) User::where(
                'role', 'freelancer')->count(),
            'total_users' => (int) User::count(),
            'user_growth' => $userGrowth,
            'freelancer_growth' => $freelancerGrowth,
            'active_projects' => (int) Project::whereIn('status', [
                'In progress',
                'Open',
            ])->count(),
            'project_growth' => $projectGrowth,
        ]);
    }

}
