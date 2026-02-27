<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Manager;
use App\Models\User;
use App\Models\Activity;
use App\Models\Task;
use App\Models\Note;


class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user || ($user->role ?? '') !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $managers = Manager::with('user')->get();
        $result = $managers->map(function ($manager) {
            return [
                'id' => $manager->id,
                'user_id' => $manager->user_id,
                'name' => $manager->user->name ?? null,
                'email' => $manager->user->email ?? null,
                'department' => $manager->department,
                'status' => $manager->status,
            ];
        });

        return response()->json($result);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $manager = Manager::where('user_id', $user->id)->first();

        return response()->json([
            'manager' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'department' => $manager->department ?? null,
            ],
            'stats' => [
                'activeTickets' => 18,
                'ticketGrowth' => 6,
                'resolved' => 42,
                'responseTime' => 1.6,
                'responseDrop' => 12,
            ],
            'tasks' => Task::where('manager_id', $manager->id)->get(),
            'notes' => Note::where('manager_id', $manager->id)->get(),
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // not used for API
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user || ($user->role ?? '') !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'department' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($data) {
            $newUser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'manager',
            ]);

            $manager = Manager::create([
                'user_id' => $newUser->id,
                'department' => $data['department'] ?? null,
                'status' => 'active',
            ]);

            return response()->json([
                'id' => $manager->id,
                'user_id' => $newUser->id,
                'name' => $newUser->name,
                'email' => $newUser->email,
                'department' => $manager->department,
                'status' => $manager->status,
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // not used
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // not used
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // optional: implement update if needed
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (! $user || ($user->role ?? '') !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $manager = Manager::find($id);
        if (! $manager) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return DB::transaction(function () use ($manager) {
            $user = $manager->user;
            if ($user) {
                $user->delete();
            } else {
                $manager->delete();
            }

            return response()->json(['message' => 'Manager deleted']);
        });
    }
}
