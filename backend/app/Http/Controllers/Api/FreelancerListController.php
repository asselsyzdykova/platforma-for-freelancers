<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class FreelancerListController extends Controller
{
    public function index()
    {
        $freelancers = User::where('role', 'freelancer')
            ->with('freelancerProfile')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => 'Frontend',
                    'about' => $user->freelancerProfile->about ?? '',
                    'rating' => $user->freelancerProfile->rating ?? 0,
                    'location' => $user->freelancerProfile->location ?? '',
                    'skills' => $user->freelancerProfile->skills ?? [],
                    'avatar_url' => $user->freelancerProfile && $user->freelancerProfile->avatar
                        ? asset('storage/avatars/' . $user->freelancerProfile->avatar)
                        : null,
                ];
            });

        return response()->json($freelancers);
    }
}
