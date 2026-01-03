<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FreelancerProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()->freelancerProfile;

        if (!$profile) {
            $profile = $request->user()->freelancerProfile()->create([]);
        }

        $profile->avatar_url = $profile->avatar
            ? asset('storage/avatars/' . $profile->avatar)
            : null;

        return response()->json($profile);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'about' => 'nullable|string',
            'location' => 'nullable|string',
            'skills' => 'nullable|array',
            'completed_projects' => 'nullable|integer',
            'proposals' => 'nullable|integer',
            'rating' => 'nullable|numeric',
            'reviews' => 'nullable|integer',
            'created_at' => 'nullable|date',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $profile = $request->user()->freelancerProfile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('avatars', $filename, 'public');

            $profile->avatar = $filename;
            $profile->save();
        }

        $profile->avatar_url = $profile->avatar
            ? asset('storage/avatars/' . $profile->avatar)
            : null;

        return response()->json($profile);
    }
}
