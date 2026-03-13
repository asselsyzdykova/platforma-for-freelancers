<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;

class ManagerVerificationController extends Controller
{
    public function index()
    {
        $candidates = User::where('role', 'freelancer')
            ->where('is_verified', false)
            ->whereHas('subscription', function ($q) {
                $q->where('plan', config('services.stripe.price_premium'))
                    ->where('status', 'active');
            })
            ->with('freelancerProfile')
            ->paginate(9);
        $formattedCandidates = $candidates->getCollection()->transform(function ($user) {
            $profile = $user->freelancerProfile;

            return [
                'id'          => $user->id,
                'name'        => $user->name,
                'role'        => 'freelancer',
                'is_pro'      => $user->is_pro,
                'is_verified' => $user->is_verified,
                'plan'        => $user->plan,
                'rating'      => $profile->rating ?? 0,
                'reviews'     => $profile->reviews ?? 0,
                'location'    => $profile->location ?? '',
                'skills'      => $profile->skills ?? [],
                'avatar_url'  => ($profile && $profile->avatar) ? Storage::url($profile->avatar) : null,
            ];
        });

        return response()->json($formattedCandidates);
    }

    public function approve(User $user)
    {
        $user->update(['is_verified' => true]);

        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Premium Verified!',
            'body' => 'Congratulations! Your profile has been verified and your blue badge is now active.',
            'type'    => 'verification_approved',
            'link'       => '/freelancer/profile',
            'related_id' => $user->id,
            'is_read' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User verified and notified.'
        ]);
    }

    public function reject(Request $request, User $user)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Verification Feedback',
            'body' => 'Your premium verification needs changes: ' . $validated['reason'],
            'type'    => 'verification_rejected',
            'link'       => '/freelancer/account',
            'related_id' => $user->id,
            'is_read' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Feedback has been sent to the user inbox.'
        ]);
    }
}
