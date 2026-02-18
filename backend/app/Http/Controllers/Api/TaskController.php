<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function getManagerTasks(Request $request)
    {
        $managerId = $request->user()->manager?->id;

        if (!$managerId) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $tasks = Task::where('manager_id', $managerId)
                     ->orderBy('deadline', 'asc')
                     ->paginate(5);

        return response()->json($tasks);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'manager_id' => 'required|exists:managers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'status' => 'nullable|string',
            ]);

        $task = Task::create([
            'manager_id' => $data['manager_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'deadline' => $data['deadline'] ?? null,
            'status' => $data['status'] ?? 'in-progress',
            ]);

        return response()->json($task, 201);
    }

}
