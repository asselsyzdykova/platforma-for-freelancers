<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Manager;
use App\Models\User;
use App\Models\Activity;
use App\Models\Task;
use App\Models\Note;
use App\Models\Ticket;
use Carbon\Carbon;

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

        $activeTick = Ticket::where('manager_id', $manager->id)
            ->whereIn('status', ['new', 'in_progress'])->count();


        $now = Carbon::now();
        $currentStart = $now->copy()->startOfMonth();
        $previousStart = $now->copy()->subMonth()->startOfMonth();
        $previousEnd = $currentStart->copy()->subSecond();

        $currentCountTicket = Ticket::where('manager_id', $manager->id)
            ->where('created_at', '>=', $currentStart)
            ->count();

        $ticketGrowth = 0;
        $previousCountTicket = Ticket::where('manager_id', $manager->id)
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();
        if ($previousCountTicket > 0) {
            $ticketGrowth = (int) round((($currentCountTicket - $previousCountTicket) / $previousCountTicket) * 100);
        } elseif ($currentCountTicket > 0) {
            $ticketGrowth = 100;
        }

        //resolved okno

        $sevenDays = Carbon::now()->subDays(7);
        $resolvedTick = Ticket::where('manager_id', $manager->id)
            ->where('status', 'resolved')->where('updated_at', '>=', $sevenDays)
            ->count();

        //avg res
        $currentTickets = Ticket::where('manager_id', $manager->id)
            ->where('status', 'resolved')
            ->whereNotNull('response')
            ->where('updated_at', '>=', $sevenDays)
            ->get();

        $avgCurrent = 0;
        if ($currentTickets->count() > 0) {
            $totalHours = $currentTickets->sum(function ($ticket) {
                return Carbon::parse($ticket->updated_at)
                    ->diffInMinutes(Carbon::parse($ticket->created_at)) / 60;
            });
            $avgCurrent = round($totalHours / $currentTickets->count(), 1);
        }

        $previousPeriodStart = Carbon::now()->subDays(14);
        $previousPeriodEnd = $sevenDays;
        $previousTickets = Ticket::where('manager_id', $manager->id)
            ->where('status', 'resolved')
            ->whereNotNull('response')
            ->whereBetween('updated_at', [$previousPeriodStart, $previousPeriodEnd])
            ->get();

        $avgPrevious = 0;
        if ($previousTickets->count() > 0) {
            $totalHoursPrev = $previousTickets->sum(function ($ticket) {
                return Carbon::parse($ticket->updated_at)
                    ->diffInMinutes(Carbon::parse($ticket->created_at)) / 60;
            });
            $avgPrevious = $totalHoursPrev / $previousTickets->count();
        }

        $responseDrop = 0;
        if ($avgPrevious > 0) {
            $responseDrop = round((($avgPrevious - $avgCurrent) / $avgPrevious) * 100);
        }

        $tasks = Task::where('manager_id', $manager->id)->get() ?? collect();

        return response()->json([
            'manager' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'department' => $manager->department ?? null,
            ],
            'stats' => [
                'activeTickets' => $activeTick,
                'ticketGrowth' => $ticketGrowth,
                'resolved' => $resolvedTick,
                'responseTime' => $avgCurrent,
                'responseDrop' => $responseDrop,
            ],
            'tasks' => $tasks,
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
