<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class FreelancerProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()->freelancerProfile;
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
        ]);

        $profile = $request->user()->freelancerProfile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json($profile);
    }
}
