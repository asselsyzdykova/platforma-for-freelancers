<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use App\Models\Manager;

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
        $manager = Manager::where('user_id', Auth::id())->firstOrFail();

        $ticket = Ticket::findOrFail($id);

        $ticket->update([
            'response' => $request->response,
            'status' => $request->status ?? 'resolved',
            'manager_id' => $manager->id,
        ]);

        return response()->json($ticket);
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
