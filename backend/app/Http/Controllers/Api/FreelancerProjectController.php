<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FreelancerProject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FreelancerProjectController extends Controller
{
    public function store(Request $request)
    {
        try {
            Log::info('Start project creation', ['user_id' => Auth::id(), 'request' => $request->all()]);

            $freelancerId = Auth::id() ?? throw new \Exception('No authenticated user');

            $data = $request->validate([
                'name' => 'required|string',
                'description' => 'nullable|string',
                'deadline' => 'nullable|date',
                'client_id' => 'required|exists:users,id',
                'milestones' => 'required|array|min:1',
                'milestones.*.title' => 'required|string',
                'milestones.*.price' => 'required|numeric|min:1',
            ]);

            Log::info('Validation passed', $data);

            $project = FreelancerProject::create([
                'freelancer_id' => $freelancerId,
                'client_id' => $data['client_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'deadline' => $data['deadline'] ?? null,
            ]);

            Log::info('Project created', ['id' => $project->id]);

            foreach ($data['milestones'] as $index => $milestone) {
                Log::info('Creating milestone #' . $index, $milestone);

                $project->milestones()->create([
                    'title' => $milestone['title'],
                    'price' => (float) $milestone['price'],  // принудительно float
                ]);
            }

            Log::info('All milestones created');

            return response()->json([
                'message' => 'Project created',
                'id' => $project->id
            ]);
        } catch (\Exception $e) {
            Log::error('Project creation failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function freelancerProjects(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $projects = FreelancerProject::with('milestones', 'client')
            ->where('freelancer_id', $request->user()->id)
            ->paginate($perPage);;

        return response()->json($projects);
    }
}
