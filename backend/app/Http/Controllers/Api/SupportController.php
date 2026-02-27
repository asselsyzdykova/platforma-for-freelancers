<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use App\Models\Manager;
use App\Models\Notification;

class SupportController extends Controller
{
    public function show($id)
    {
        $ticket = Ticket::with(['manager.user'])->findOrFail($id);
        
        if ($ticket->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return response()->json($ticket);
    }
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
        $manager = Manager::where('user_id', Auth::id())->firstOrFail();

        $ticket = Ticket::findOrFail($id);

        $ticket->update([
            'response' => $request->response,
            'status' => $request->status ?? 'resolved',
            'manager_id' => $manager->id,
        ]);

        Notification::create([
            'user_id' => $ticket->user_id,
            'type' => 'support_response',
            'title' => 'Support replied to your ticket',
            'body' => 'Your ticket "' . $ticket->subject . '" has been answered.',
            'link' => '/support-answer/' . $ticket->id,
            'related_id' => $ticket->id,
            'is_read' => false,
        ]);

        return response()->json($ticket, 201);
    }

    public function latest()
    {
        return Ticket::with('user')
            ->latest()
            ->take(5)
            ->get();
    }

    public function allTickets(Request $request)
    {
        $query = Ticket::with('user')->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return $query->paginate(9);
    }
}
