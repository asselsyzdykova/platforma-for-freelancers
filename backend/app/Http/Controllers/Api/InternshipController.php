<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 9);
        $query = Internship::with('client.clientProfile');
        $paginator = $query->paginate($perPage);
        $paginator->getCollection()->transform(function ($intern) {
            return [
                'id' => $intern->id,
                'name' => $intern->client->name ?? null,
                'avatar_url' => optional($intern->client->clientProfile)->avatar_url,
                'company' => $intern->company,
                'title' => $intern->title,
                'description' => $intern->description,
                'stipendType' => $intern->stipendType,
                'price' => $intern->stipendType === 'paid' ? "€" . $intern->price : 'Unpaid',
                'time' => $intern->time,
                'number' => $intern->number,
            ];
        });
        return response()->json($paginator);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'company' => 'required|string',
            'description' => 'required|string',
            'stipendType' => 'required|in:paid,unpaid',
            'price' => 'nullable|string',
            'time' => 'required|string',
            'number' => 'required|integer',
        ]);

        $internship = Internship::create([
            'client_id' => Auth::id(),
            'title' => $request->title,
            'company' => $request->company,
            'description' => $request->description,
            'stipendType' => $request->stipendType,
            'price' => $request->price,
            'time' => $request->time,
            'number' => $request->number,
        ]);

        $internship->load('client.clientProfile');
        return response()->json($internship, 201);
    }

    public function apply(Internship $internship)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        if ($user->role !== 'freelancer') {
            return response()->json([
                'message' => 'Only freelancers can apply.'
            ], 403);
        }

        $alreadyApplied = DB::table('internship_applications')
            ->where('internship_id', $internship->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyApplied) {
            return response()->json(
                ['message' => 'You already applied to this internship.'],
                422
            );
        }

        if ($internship->number <= 0) {
            return response()->json([
                'message' => 'No available positions.'
            ], 422);
        }

        DB::table('internship_applications')->insert([
            'internship_id' => $internship->id,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $internship->decrement('number');
        DB::table('notifications')->insert([
            'user_id' => $internship->client->id,
            'type' => 'internship_application',
            'title' => 'New application',
            'body' => $user->name . ' applied to your internship: ' . $internship->title,
            'link' => '/internships/' . $internship->id,
            'related_id' => $internship->id,
            'is_read' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Applied successfully'
        ]);
    }
    /**
     * Display the specified resource.
     */

    public function my(Request $request)
    {
        $userId = Auth::id();
        $perPage = (int) $request->query('per_page', 9);
        $perPage = $perPage > 0 ? min($perPage, 50) : 9;
        $query = Internship::where('client_id', $userId)
            ->with('client.clientProfile');

        $paginator = $query->paginate($perPage);

        $paginator->getCollection()->transform(function ($intern) {
            return [
                'id' => $intern->id,
                'title' => $intern->title,
                'company' => $intern->company,
                'description' => $intern->description,
                'stipendType' => $intern->stipendType,
                'price' => $intern->stipendType === 'paid' ? "€" . $intern->price : 'Unpaid',
                'time' => $intern->time,
                'number' => $intern->number
            ];
        });

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
    public function show(string $id)
    {
        $internship = Internship::with('client')->findOrFail($id);
        return response()->json($internship);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $internship = Internship::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string',
            'company' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'stipendType' => 'sometimes|required|in:paid,unpaid',
            'price' => 'nullable|string',
            'time' => 'sometimes|required|string',
            'number' => 'sometimes|required|integer',
        ]);

        $internship->update($request->all());

        $internship->load('client');

        return response()->json($internship);
    }

    /**
     * Remove the specified resource from storage.
     */

    public function applications($internshipId, Request $request)
    {
        $perPage = $request->query('per_page', 9);

        $internship = Internship::findOrFail($internshipId);

        if (Auth::id() !== $internship->client_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $applications = DB::table('internship_applications')
            ->where('internship_id', $internshipId)
            ->join('users', 'users.id', '=', 'internship_applications.user_id')
            ->select(
                'internship_applications.id',
                'internship_applications.created_at',
                'users.id as freelancer_id',
                'users.name',
                'users.university',
            )
            ->latest('internship_applications.created_at')
            ->paginate($perPage);

        return response()->json($applications);
    }
    public function destroy(string $id)
    {
        $internship = Internship::findOrFail($id);
        $internship->delete();

        return response()->json(['message' => 'Internship deleted successfully']);
    }
}
