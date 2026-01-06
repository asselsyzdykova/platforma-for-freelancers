<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalController extends Controller
{
    public function apply(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        $data = $request->validate([
            'message' => 'required|string',
            'budget' => 'required|numeric',
        ]);

        $proposal = $project->proposals()->create([
            'freelancer_id' => Auth::id(),
            'message' => $data['message'],
            'budget' => $data['budget'],
        ]);

        Notification::create([
            'user_id' => $project->client_id,
            'type' => 'project_application',
            'title' => 'New application',
            'body' => 'A freelancer has applied to your project: ' . $project->title,
            'link' => '/projects/' . $project->id,
        ]);

        return response()->json($proposal, 201);
    }
}
