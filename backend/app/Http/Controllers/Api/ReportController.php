<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reported_user_id' => 'required|exists:users,id',
            'ticket_id' => 'nullable|exists:tickets,id',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',

        ]);

        $report = Report::create([
            'reporter_id' => Auth::id(),
            'reported_user_id' => $validated['reported_user_id'],
            'ticket_id' => $validated['ticket_id'] ?? null,
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);
        return response()->json($report, 201);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $query = Report::with(['reporter', 'reportedUser', 'ticket'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->paginate(9);

        return response()->json($reports);
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved'
        ]);

        $report = Report::findOrFail($id);
        $report->status = $request->status;
        $report->save();

        return response()->json([
            'message' => 'Report updated successfully',
            'report' => $report
        ]);
    }
}
