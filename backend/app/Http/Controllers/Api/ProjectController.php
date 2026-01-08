<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::with('client.clientProfile')->latest()->get();
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
