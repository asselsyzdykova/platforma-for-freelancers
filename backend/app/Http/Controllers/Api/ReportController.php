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
}
