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
            'status' => 'Pending',
        ]);

        Notification::create([
            'user_id' => $project->client_id,
            'type' => 'project_application',
            'title' => 'New message',
            'body' => 'A freelancer has applied to your project: ' . $project->title,
            'link' => '/application-details/' . $proposal->id,
            'related_id' => $proposal->id,
        ]);

        return response()->json($proposal, 201);
    }

    public function show($id)
    {
        $proposal = Proposal::with(['freelancer', 'project'])->findOrFail($id);

        if (Auth::id() !== $proposal->project->client_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'id' => $proposal->id,
            'freelancer' => [
                'id' => $proposal->freelancer->id,
                'name' => $proposal->freelancer->name,
                'avatar_url' => $proposal->freelancer->avatar_url ?? null,
            ],
            'project' => [
                'id' => $proposal->project->id,
                'title' => $proposal->project->title,
            ],
            'proposal_text' => $proposal->message,
            'budget' => $proposal->budget,
            'status' => $proposal->status ?? 'Pending',
            'created_at' => $proposal->created_at,
        ]);
    }

    public function review($id)
    {
        logger()->info('Review request received', ['user_id' => Auth::id(), 'proposal_id' => $id]);

        $proposal = Proposal::with(['project', 'freelancer'])->findOrFail($id);
        if (Auth::id() !== $proposal->project->client_id) {
            logger()->warning('Forbidden review attempt', ['user_id' => Auth::id(), 'proposal_id' => $id]);
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $proposal->status = 'Under Review';
            $proposal->save();
            logger()->info('Proposal status updated to Under Review', ['proposal_id' => $proposal->id]);
        } catch (\Throwable $e) {
            logger()->error('Failed to update proposal status: ' . $e->getMessage(), ['proposal_id' => $id]);
            return response()->json(['message' => 'Failed to update proposal status'], 500);
        }

        try {
            Notification::create([
                'user_id' => $proposal->freelancer_id,
                'type' => 'application_status',
                'title' => 'Application under review',
                'body' => 'Your application for "' . $proposal->project->title . '" is under review by the client.',
                'link' => '/freelancer/inbox',
                'related_id' => $proposal->id,
            ]);
            logger()->info('Notification created for review', ['to_user' => $proposal->freelancer_id, 'proposal_id' => $proposal->id]);
        } catch (\Exception $e) {
            logger()->error('Failed to create notification for review: ' . $e->getMessage(), ['proposal_id' => $proposal->id]);
        }

        return response()->json(['message' => 'Marked under review']);
    }

    public function accept($id)
    {
        $proposal = Proposal::with(['project', 'freelancer'])->findOrFail($id);
        if (Auth::id() !== $proposal->project->client_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $proposal->status = 'Accepted';
        $proposal->save();

        try {
            Notification::create([
                'user_id' => $proposal->freelancer_id,
                'type' => 'application_status',
                'title' => 'Application accepted',
                'body' => 'Congratulations! Your application for "' . $proposal->project->title . '" was accepted.',
                'link' => '/freelancer/inbox',
                'related_id' => $proposal->id,
            ]);
        } catch (\Exception $e) {
            logger()->error('Failed to create notification for accept: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Proposal accepted']);
    }

    public function reject($id)
    {
        $proposal = Proposal::with(['project', 'freelancer'])->findOrFail($id);
        if (Auth::id() !== $proposal->project->client_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $proposal->status = 'Rejected';
        $proposal->save();

        try {
            Notification::create([
                'user_id' => $proposal->freelancer_id,
                'type' => 'application_status',
                'title' => 'Application rejected',
                'body' => 'We are sorry. Your application for "' . $proposal->project->title . '" was not selected.',
                'link' => '/freelancer/inbox',
                'related_id' => $proposal->id,
            ]);
        } catch (\Exception $e) {
            logger()->error('Failed to create notification for reject: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Proposal rejected']);
    }
}
