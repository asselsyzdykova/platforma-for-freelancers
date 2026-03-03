<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Models\Manager;
use Carbon\Carbon;
use App\Models\Subscription;
use App\Exports\AdminReportExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Notification;
use App\Models\Report;

class AdminController extends Controller
{
    public function adminStats(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        if (($user->role ?? '') !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $now = Carbon::now();
        $currentStart = $now->copy()->startOfMonth();
        $previousStart = $now->copy()->subMonth()->startOfMonth();
        $previousEnd = $currentStart->copy()->subSecond();

        $currentCount = User::where('created_at', '>=', $currentStart)->count();
        $previousCount = User::whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $userGrowth = 0;
        if ($previousCount > 0) {
            $userGrowth = (int) round((($currentCount - $previousCount) / $previousCount) * 100);
        } elseif ($currentCount > 0) {
            $userGrowth = 100;
        }

        $currentCountFreelancer = User::where('role', 'freelancer')->where('created_at', '>=', $currentStart)->count();

        $previousCountFreelancer = User::where('role', 'freelancer')->whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $freelancerGrowth = 0;
        if ($previousCountFreelancer > 0) {
            $freelancerGrowth = (int) round((($currentCountFreelancer - $previousCountFreelancer) /
                $previousCountFreelancer) * 100);
        } elseif ($currentCountFreelancer > 0) {
            $freelancerGrowth = 100;
        }

        //active projects
        $currentCountProject = Project::where('created_at', '>=', $currentStart)->count();
        $previousCountProject = Project::whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $projectGrowth = 0;
        if ($previousCountProject > 0) {
            $projectGrowth = (int) round((($currentCountProject - $previousCountProject) / $previousCountProject) * 100);
        } elseif ($currentCountProject > 0) {
            $projectGrowth = 100;
        }

        $userGrowth = min($userGrowth, 100);
        $freelancerGrowth = min($freelancerGrowth, 100);
        $projectGrowth = min($projectGrowth, 100);

        return response()->json([
            'total_freelancers' => (int) User::where(
                'role',
                'freelancer'
            )->count(),
            'total_users' => (int) User::count(),
            'user_growth' => $userGrowth,
            'freelancer_growth' => $freelancerGrowth,
            'active_projects' => (int) Project::whereIn('status', [
                'In progress',
                'Open',
            ])->count(),
            'project_growth' => $projectGrowth,

            //recent signups

            'recent_signups' => User::latest()->take(5)->get(['id', 'name', 'email', 'role', 'university', 'created_at']),

            'subscriptions' => [
                'subs_count' => (int) Subscription::count(),
                'subs_active' => (int) Subscription::where('status', 'active')->count(),
                'subs_canceled' => (int) Subscription::where('status', 'canceled')->count(),
                'subs_free' => (int) Subscription::where('provider_id', 'free')->count(),
            ],
        ]);
    }

    public function getManagers(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $managers = Manager::with('user')->get();
        $result = $managers->map(function ($manager) {
            return [
                'id' => $manager->id,
                'name' => $manager->user->name,
                'email' => $manager->user->email,
                'department' => $manager->department,
                'status' => $manager->status,
            ];
        });
        return response()->json($result);
    }

    //excel
    public function exportReport(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return Excel::download(new AdminReportExport, 'admin_report.xlsx');
    }

    //block
    public function blockUser($id)
    {
        $user = User::findOrFail($id);

        $user->blocked = true;
        $user->save();

        return response()->json([
            'message' => 'User blocked successfully'
        ]);
    }

    //warn
    public function warnUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);
        $user = User::findOrFail($request->user_id);
        $report = Report::create([
            'reporter_id' => $request->user()->id,
            'reported_user_id' => $user->id,
            'ticket_id' => null,
            'reason' => 'admin_warning',
            'description' => $request->message,
            'status' => 'new',
        ]);
        $user = User::findOrFail($request->user_id);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'admin_warning',
            'title' => 'Warning from Admin',
            'body' => $request->message,
            'link' => '/report-answer/' . $report->id,
            'related_id' => $report->id,
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Warning sent successfully'
        ]);
    }

    public function getReport($id)
    {
        $report = Report::with('reporter', 'reportedUser')->find($id);

        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }

        return response()->json($report);
    }
}
