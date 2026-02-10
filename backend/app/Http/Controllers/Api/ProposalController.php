<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Notification;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProposalController extends Controller
{
    public function apply(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        $user = $request->user();
        if (!$user || $user->role !== 'freelancer') {
            return response()->json(['message' => 'Only freelancers can apply'], 403);
        }

        $plan = $this->getActivePlan($user->id);
        if ($plan === 'free') {
            $startOfMonth = Carbon::now()->startOfMonth();
            $count = Proposal::where('freelancer_id', $user->id)
                ->where('created_at', '>=', $startOfMonth)
                ->count();

            if ($count >= 3) {
                return response()->json([
                    'message' => 'Free plan allows only 3 proposals per month. Upgrade to Pro or Premium.',
                ], 403);
            }
        }

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

        $profile = $user->freelancerProfile;
        if ($profile) {
            $profile->proposals = Proposal::where('freelancer_id', $user->id)->count();
            $profile->save();
        }

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

    private function getActivePlan(int $userId): string
    {
        $subscription = Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->latest('created_at')
            ->first();

        if (!$subscription) {
            return 'free';
        }

        $plan = $subscription->plan;

        if ($plan === env('STRIPE_PRICE_PRO') || $plan === 'pro') {
            return 'pro';
        }

        if ($plan === env('STRIPE_PRICE_PREMIUM') || $plan === 'premium') {
            return 'premium';
        }

        if ($plan === 'free') {
            return 'free';
        }

        return 'pro';
    }

    public function show($id)
    {
        $proposal = Proposal::with(['freelancer', 'project'])->findOrFail($id);

        if (Auth::id() !== $proposal->project->client_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $projectId = $proposal->project_id;
        $totalProposals = Proposal::where('project_id', $projectId)->count();
        $position = Proposal::where('project_id', $projectId)
            ->where(function ($query) use ($proposal) {
                $query->where('created_at', '>', $proposal->created_at)
                    ->orWhere(function ($q) use ($proposal) {
                        $q->where('created_at', $proposal->created_at)
                          ->where('id', '>', $proposal->id);
                    });
            })
            ->count() + 1;

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
            'project_proposals_total' => $totalProposals,
            'project_proposal_position' => $position,
        ]);
    }

    public function view($id)
    {
        $proposal = Proposal::with(['project', 'freelancer'])->findOrFail($id);
        if (Auth::id() !== $proposal->project->client_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($proposal->status === 'Pending') {
            $proposal->status = 'Viewed';
            $proposal->save();

            try {
                Notification::create([
                    'user_id' => $proposal->freelancer_id,
                    'type' => 'application_status',
                    'title' => 'Application viewed',
                    'body' => 'Your application for "' . $proposal->project->title . '" has been viewed by the client.',
                    'link' => '/freelancer/inbox',
                    'related_id' => $proposal->id,
                ]);
            } catch (\Exception $e) {
                logger()->error('Failed to create notification for view: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Application viewed']);
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

    public function freelancerProposals()
    {
        $perPage = (int) request()->get('per_page', 8);
        $perPage = $perPage > 0 ? min($perPage, 50) : 8;

        $paginated = Proposal::where('freelancer_id', Auth::id())
            ->with(['project', 'project.client'])
            ->latest()
            ->paginate($perPage);

        $proposals = collect($paginated->items())->map(function ($proposal) {
            return [
                'id' => $proposal->id,
                'project_id' => $proposal->project_id,
                'project_name' => $proposal->project->title,
                'status' => $proposal->status ?? 'Pending',
                'created_at' => $proposal->created_at,
            ];
        });

        return response()->json([
            'data' => $proposals,
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function projectProposals(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        if (Auth::id() !== $project->client_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $perPage = (int) $request->get('per_page', 6);
        $perPage = $perPage > 0 ? min($perPage, 50) : 6;

        $paginated = Proposal::where('project_id', $projectId)
            ->with('freelancer')
            ->latest('created_at')
            ->paginate($perPage);

        $proposals = collect($paginated->items())->map(function ($proposal) {
            return [
                'id' => $proposal->id,
                'freelancer' => [
                    'id' => $proposal->freelancer->id,
                    'name' => $proposal->freelancer->name,
                    'avatar_url' => $proposal->freelancer->avatar_url ?? null,
                ],
                'message' => $proposal->message,
                'budget' => $proposal->budget,
                'status' => $proposal->status ?? 'Pending',
                'created_at' => $proposal->created_at,
            ];
        });

        return response()->json([
            'data' => $proposals,
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
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
