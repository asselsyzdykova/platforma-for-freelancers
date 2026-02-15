<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Rules\SlovakUniversityEmail;

class AuthController extends Controller
{
    public function register(Request $request)
{
    $emailRules = [
        'required',
        'string',
        'email',
        'max:255',
        'unique:users',
    ];

    if ($request->role === 'freelancer') {
        $emailRules[] = new SlovakUniversityEmail;
    }

    $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            function ($attribute, $value, $fail) {
                if (!preg_match('/^[A-ZА-ЯЁ][a-zа-яё]+\s+[A-ZА-ЯЁ][a-zа-яё]+$/u', $value)) {
                    $fail('Name must include first and last name, each starting with a capital letter.');
                }
            },
        ],
        'email' => $emailRules,
        'university' => 'nullable|string|max:255',
        'study_year' => 'nullable|string|max:255',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'required|in:user,freelancer,manager',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'university' => $request->university,
        'study_year' => $request->study_year,
        'password' => Hash::make($request->password),
        'role' => $request->role,
    ]);

    if ($user->role === 'freelancer') {
        Subscription::firstOrCreate(
            [
                'user_id' => $user->id,
                'provider' => 'internal',
                'provider_id' => 'free',
            ],
            [
                'plan' => 'free',
                'status' => 'active',
                'start_date' => now(),
                'end_date' => null,
            ]
        );
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user,
    ]);
}


 public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Incorrect email or password'],
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
