<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ClientProjectController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 3);
        $perPage = $perPage > 0 ? min($perPage, 50) : 3;

        $query = Project::where('client_id', Auth::id());

        $paginated = $query->latest('created_at')->paginate($perPage);

        $totalCount = (int) $query->count();
        $activeCount = (int) Project::where('client_id', Auth::id())
            ->where('status', 'In progress')
            ->count();
        $completedCount = (int) Project::where('client_id', Auth::id())
            ->where('status', 'Completed')
            ->count();

        return response()->json([
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'stats' => [
                    'posted' => $totalCount,
                    'active' => $activeCount,
                    'completed' => $completedCount,
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|string',
            'category' => 'required|string',
            'tags' => 'array',
        ]);

        $project = Project::create([
            'client_id' => Auth::id(),
            'title' => $data['title'],
            'description' => $data['description'],
            'budget' => $data['budget'],
            'category' => $data['category'],
            'tags' => $data['tags'] ?? [],
            'status' => 'In progress',
        ]);

        return response()->json($project, 201);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        if ($project->client_id !== Auth::id()) {
            return response()->json(['error' => 'Not your project'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'budget' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:100',
            'tags' => 'nullable|array',
            'status' => 'sometimes|string'
        ]);

        $project->update($validated);

        return response()->json($project);
    }
    public function show(Project $project)
    {
        return response()->json($project);
    }
}
