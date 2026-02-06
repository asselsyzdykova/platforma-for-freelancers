<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
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

        $certificates = is_array($profile->certificates) ? $profile->certificates : [];
        $profile->certificate_urls = array_values(array_map(function ($certificate) {
            return asset('storage/certificates/' . $certificate);
        }, $certificates));

        $profile->proposals = Proposal::where('freelancer_id', $request->user()->id)->count();

        return response()->json($profile);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'about' => 'nullable|string',
            'location' => 'nullable|string',
            'skills' => 'nullable|string',
            'completed_projects' => 'nullable|integer|min:0',
            'proposals' => 'nullable|integer|min:0',
            'avatar' => 'nullable|image|max:2048',
            'certificates' => 'nullable|array|max:20',
            'certificates.*' => 'file|mimes:png,jpg,jpeg|max:4096',
            'certificates_existing' => 'nullable',
        ]);

        if (!empty($validated['skills']) && is_string($validated['skills'])) {
            $validated['skills'] = json_decode($validated['skills'], true);
        }

        $data = collect($validated)->except(['avatar', 'certificates', 'certificates_existing'])->toArray();

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
            $files = $request->file('certificates');
            foreach ($files as $file) {
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

        $profile->proposals = Proposal::where('freelancer_id', $request->user()->id)->count();

        return response()->json($profile);
    }
}
