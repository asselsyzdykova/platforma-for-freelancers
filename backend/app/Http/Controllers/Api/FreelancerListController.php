<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class FreelancerListController extends Controller
{
    public function index()
    {
        $search = request()->string('search')->trim()->value();
        $category = request()->string('category')->trim()->value();
        $perPage = (int) request()->get('per_page', 8);
        $perPage = $perPage > 0 ? min($perPage, 50) : 8;

        $query = User::where('role', 'freelancer')->with('freelancerProfile');

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
}
