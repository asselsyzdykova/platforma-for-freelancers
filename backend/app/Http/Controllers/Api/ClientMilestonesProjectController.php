<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use App\Models\FreelancerProject;

class ClientMilestonesProjectController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 3);
        $perPage = $perPage > 0 ? min($perPage, 50) : 3;

        $query = FreelancerProject::where('client_id', Auth::id())
            ->with(['freelancer', 'milestones']);

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
}
