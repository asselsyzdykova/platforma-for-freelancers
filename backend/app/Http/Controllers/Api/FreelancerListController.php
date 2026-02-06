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
                $certificates = $user->freelancerProfile->certificates ?? [];
                if (!is_array($certificates)) {
                    $certificates = [];
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => 'freelancer',
                    'about' => $user->freelancerProfile->about ?? '',
                    'rating' => $user->freelancerProfile->rating ?? 0,
                    'reviews' => $user->freelancerProfile->reviews ?? 0,
                    'location' => $user->freelancerProfile->location ?? '',
                    'skills' => $user->freelancerProfile->skills ?? [],
                    'avatar_url' => $user->freelancerProfile && $user->freelancerProfile->avatar
                        ? asset('storage/avatars/' . $user->freelancerProfile->avatar)
                        : null,
                    'certificate_urls' => array_values(array_map(function ($certificate) {
                        return asset('storage/certificates/' . $certificate);
                    }, $certificates)),
                ];
            });

        return response()->json($freelancers);
    }
}
