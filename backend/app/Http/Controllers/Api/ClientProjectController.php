<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ClientProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('client_id', Auth::id())->get();
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|string',
            'category' => 'required|string',
            'tags' => 'array',
        ]);

        $project = Project::create([
            'client_id' => Auth::id(),
            'title' => $data['title'],
            'description' => $data['description'],
            'budget' => $data['budget'],
            'category' => $data['category'],
            'tags' => $data['tags'] ?? [],
            'status' => 'In progress',
        ]);

        return response()->json($project, 201);
    }
}
