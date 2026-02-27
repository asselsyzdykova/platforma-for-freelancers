<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'description' => $request->description,
        ]);
    }

    public function index()
    {
        return Ticket::with(['user', 'manager'])->get();
    }

    public function myTickets()
    {
        return Ticket::where('user_id', Auth::id())->get();
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update([
            'response' => $request->response,
            'status' => $request->status ?? 'resolved',
            'manager_id' => Auth::id(),
        ]);

        return response()->json($ticket);
    }
}
