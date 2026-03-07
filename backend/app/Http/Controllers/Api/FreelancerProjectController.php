<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FreelancerProject;
use Illuminate\Support\Facades\Auth;

class FreelancerProjectController extends Controller
{
    public function store(Request $request)
    {
        $freelancerId = Auth::id();

        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'client_id' => 'required|exists:users,id',

            'milestones' => 'required|array|min:1',
            'milestones.*.title' => 'required|string',
            'milestones.*.price' => 'required|numeric|min:1'
        ]);

        $project = FreelancerProject::create([
            'freelancer_id' => $freelancerId,
            'client_id' => $data['client_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'deadline' => $data['deadline'] ?? null,
        ]);

        foreach ($data['milestones'] as $milestone) {
            $project->milestones()->create([
                'title' => $milestone['title'],
                'price' => $milestone['price']
            ]);
        }

        return response()->json([
            'message' => 'Project created',
            'project' => $project->load('milestones')
        ]);
    }
}
