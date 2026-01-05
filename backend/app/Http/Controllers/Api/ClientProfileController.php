<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()
            ->clientProfile()
            ->with('user')
            ->first();

        return response()->json($profile);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
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
            $filename = time().'_'.$request->avatar->getClientOriginalName();
            $request->avatar->storeAs('avatars', $filename, 'public');

            $profile->avatar = $filename;
            $profile->save();
        }

        return response()->json($profile->load('user'));
    }
}
