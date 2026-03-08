<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()->clientProfile()->firstOrCreate([]);
        $profile->load('user');

        $profile->avatar_url = $profile->avatar
            ? Storage::url($profile->avatar)
            : null;

        return response()->json($profile);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string',
            'company' => 'nullable|string',
            'location' => 'nullable|string',
            'about' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $profile = $request->user()->clientProfile()
            ->updateOrCreate(
                ['user_id' => $request->user()->id],
                $data
            );

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            if ($profile->avatar) {
                Storage::disk('s3')->delete($profile->avatar);
            }
            $path = $file->storeAs('avatars', $filename, 's3');
            $profile->avatar = $path;
            $profile->save();
        }

        if (!empty($data['name'])) {
            $user = $request->user();
            $user->name = $data['name'];
            $user->save();
        }

        $profile->avatar_url = $profile->avatar
            ? Storage::url($profile->avatar)
            : null;
        return response()->json($profile->load('user'));
    }
}
