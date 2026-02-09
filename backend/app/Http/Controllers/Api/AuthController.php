<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
{

        $allowedDomains = [
            '@uniba.sk',  //1Univerzita Komenského v Bratislave
            '@student.uniba.sk',
            '@stuba.sk', //2Slovenská technická univerzita v Bratislave
            '@student.stuba.sk',
            '@tuke.sk', //3Technická univerzita v Košiciach
            '@student.tuke.sk',
            '@tuzvo.sk', //4Technická univerzita vo Zvolene
            '@student.tuzvo.sk',
            '@ukf.sk', //5Univerzita Konštantína Filozofa v Nitre
            '@student.ukf.sk',
            '@umb.sk', //6Univerzita Mateja Bela v Banskej Bystrici
            '@student.umb.sk',
            '@upjs.sk', //7Univerzita Pavla Jozefa Šafárika v Košiciach
            '@student.upjs.sk',
            '@uniag.sk', //8Slovenská poľnohospodárska univerzita v Nitre
            '@student.uniag.sk',
            '@euba.sk', //9Ekonomická univerzita v Bratislave
            '@student.euba.sk',
            '@unipo.sk', //10Prešovská univerzita v Prešove
            '@student.unipo.sk',
            '@uniza.sk', //11Žilinská univerzita v Žiline
            '@student.uniza.sk',
            '@uvm.sk', //12Univerzita veterinárskeho lekárstva a farmácie v Košiciach
            '@student.uvm.sk',
            '@truni.sk', //13Trnavská univerzita v Trnave
            '@student.truni.sk',
            '@ucm.sk', //14Univerzita sv. Cyrila a Metoda v Trnave
            '@student.ucm.sk',
            '@szu.sk', //15Slovenská zdravotnícka univerzita v Bratislave
            '@student.szu.sk',
        ];

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
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) use ($allowedDomains, $request) {
                    if ($request->role !== 'freelancer') {
                        return;
                    }

                    $email = strtolower($value);
                    $isAllowed = false;
                    foreach ($allowedDomains as $domain) {
                        if (str_ends_with($email, $domain)) {
                            $isAllowed = true;
                            break;
                        }
                    }

                    if (! $isAllowed) {
                        $fail('Freelancer registration is limited to Slovak university email domains.');
                    }
                },
            ],
        'university' => 'nullable|string|max:255',
        'study_year' => 'nullable|string|max:255',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'required|in:user,freelancer',
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
