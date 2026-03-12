<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FreelancerProfile;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class FreelancerListController extends Controller
{
    public function index()
    {
        $search = request()->string('search')->trim()->value();
        $category = request()->string('category')->trim()->value();
        $perPage = (int) request()->get('per_page', 8);
        $perPage = $perPage > 0 ? min($perPage, 50) : 8;

        $query = User::where('role', 'freelancer')->with('freelancerProfile', 'subscription');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhereHas('freelancerProfile', function ($profileQuery) use ($search) {
                        $profileQuery->where('skills', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($category) {
            $query->whereHas('freelancerProfile', function ($profileQuery) use ($category) {
                $profileQuery->whereJsonContains('skills', $category);
            });
        }

        $paginated = $query->orderBy('name')->paginate($perPage);

        $freelancers = $paginated->getCollection()->map(function ($user) {
            $profile = $user->freelancerProfile;
            $certificates = $user->freelancerProfile->certificates ?? [];
            if (!is_array($certificates)) {
                $certificates = [];
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => 'freelancer',
                'about' => $user->freelancerProfile->about ?? '',
                'is_pro' => $user->is_pro,
                'is_verified' => $user->is_verified,
                'plan' => $user->plan,
                'rating' => $user->freelancerProfile->rating ?? 0,
                'reviews' => $user->freelancerProfile->reviews ?? 0,
                'location' => $user->freelancerProfile->location ?? '',
                'skills' => $user->freelancerProfile->skills ?? [],
                'avatar_url' => ($profile && $profile->avatar) ? Storage::url($profile->avatar) : null,
                'certificate_urls' => array_values(array_map(function ($certificate) {
                    return Storage::url($certificate);
                }, $certificates)),
            ];
        });

        return response()->json([
            'data' => $freelancers,
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function show($id)
    {
        $user = User::where('role', 'freelancer')
            ->with('freelancerProfile')
            ->find($id);

        if (!$user) {
            return response()->json(['message' => 'Freelancer not found'], 404);
        }

        $profile = $user->freelancerProfile;
        $certificates = $profile->certificates ?? [];
        if (!is_array($certificates)) $certificates = [];

        return response()->json([
            'id'               => $user->id,
            'name'             => $user->name,
            'role'             => $user->role,
            'about'            => $profile->about ?? '',
            'rating'           => $profile->rating ?? 0,
            'reviews'          => $profile->reviews ?? 0,
            'location'         => $profile->location ?? '',
            'skills'           => $profile->skills ?? [],
            'avatar_url' => ($profile && $profile->avatar) ? Storage::url($profile->avatar) : null,
            'certificate_urls' => array_map(fn($c) => Storage::url($c), $certificates),
            'university'       => $user->university,
            'study_year'       => $user->study_year,
        ]);
    }

    public function skills()
    {
        $skills = FreelancerProfile::pluck('skills')
            ->flatten()
            ->unique()
            ->filter()
            ->values();
        return response()->json($skills);
    }
}
