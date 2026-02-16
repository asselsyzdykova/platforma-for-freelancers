<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Rules\SlovakUniversityEmail;
use \App\Models\FreelancerProfile;


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

        $certificates = is_array($profile->certificates) ? $profile->certificates : [];

        $profile->certificate_urls = array_values(array_map(function ($certificate) {
            return asset('storage/certificates/' . $certificate);
        }, $certificates));

        $profile->proposals = Proposal::where(
            'freelancer_id',
            $request->user()->id
        )->count();

        return response()->json($profile);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'about' => 'nullable|string',
            'location' => 'nullable|string',
            'skills' => 'nullable|string',
            'completed_projects' => 'nullable|integer|min:0',
            'avatar' => 'nullable|image|max:2048',
            'certificates' => 'nullable|array|max:20',
            'certificates.*' => 'file|mimes:png,jpg,jpeg|max:4096',
            'certificates_existing' => 'nullable',
        ]);

        
        if (!empty($validated['skills']) && is_string($validated['skills']))
            {
            $decoded = json_decode($validated['skills'], true);
            if (is_array($decoded))
                {
                $normalizedSkills = [];

                foreach ($decoded as $skill)
                    {
                    $clean = ucfirst(strtolower(trim($skill)));
                    if (!in_array($clean, $normalizedSkills))
                        {
                        $normalizedSkills[] = $clean;
                        }
                    }
                        $validated['skills'] = $normalizedSkills;
                }
            }

        $data = collect($validated)
            ->except(['avatar', 'certificates', 'certificates_existing'])
            ->toArray();

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


        $existing = $request->input('certificates_existing', []);

        if (is_string($existing)) {
            $decoded = json_decode($existing, true);
            $existing = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($existing)) {
            $existing = [];
        }

        if ($request->hasFile('certificates')) {
            foreach ($request->file('certificates') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->storeAs('certificates', $filename, 'public');
                $existing[] = $filename;
            }
        }

        $profile->certificates = $existing;
        $profile->save();


        $profile->avatar_url = $profile->avatar
            ? asset('storage/avatars/' . $profile->avatar)
            : null;

        $profile->certificate_urls = array_values(array_map(function ($certificate) {
            return asset('storage/certificates/' . $certificate);
        }, $existing));

        $profile->proposals = Proposal::where(
            'freelancer_id',
            $request->user()->id
        )->count();

        return response()->json($profile);
    }

    public function updateAccount(Request $request)
{
    $user = $request->user();

    $emailRules = [
        'required',
        'email',
        'unique:users,email,' . $user->id,
    ];

    if ($user->role === 'freelancer') {
        $emailRules[] = new SlovakUniversityEmail;
    }

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => $emailRules,
        'current_password' => 'nullable|string',
        'new_password' => 'nullable|string|min:6|confirmed',
    ]);

    if ($request->filled('new_password')) {
        if (!$request->filled('current_password')) {
            return response()->json([
                'message' => 'Current password is required'
            ], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->password = Hash::make($validated['new_password']);
    }

    $user->name = $validated['name'];
    $user->email = $validated['email'];
    $user->save();

    return response()->json([
        'message' => 'Account updated successfully',
        'user' => $user
    ]);
}



    public function destroy(Request $request)
    {
        $user = $request->user();

        $user->freelancerProfile()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully'
        ]);
    }


    public function allSkills()
{
    $profiles = FreelancerProfile::whereNotNull('skills')->get();

    $allSkills = [];

    foreach ($profiles as $profile) {
        $skills = is_array($profile->skills)
            ? $profile->skills
            : json_decode($profile->skills, true);

        if (is_array($skills)) {
            foreach ($skills as $skill) {
                $normalized = ucfirst(strtolower(trim($skill)));
                $allSkills[] = $normalized;
            }
        }
    }

    $uniqueSkills = array_values(array_unique($allSkills));
    sort($uniqueSkills);

    return response()->json($uniqueSkills);
}

}
