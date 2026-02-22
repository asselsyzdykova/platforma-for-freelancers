<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $perPage = (int) request()->get('per_page', 8);
        $perPage = $perPage > 0 ? min($perPage, 50) : 8;

        $paginated = Project::with('client.clientProfile')
            ->withCount([
                'proposals as already_applied' => function ($q) {
                    $q->where('freelancer_id', Auth::id());
                }
            ])
            ->latest()->paginate($perPage);

        return response()->json([
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        if ($project->client_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $project->delete();

        return response()->json(['message' => 'Project deleted successfully']);
    }
}
