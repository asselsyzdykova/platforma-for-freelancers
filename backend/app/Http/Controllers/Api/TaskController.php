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
            'status' => $data['status'] ?? null,
            ]);

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
{
    $managerId = $request->user()->manager?->id;

    if (!$managerId || $task->manager_id !== $managerId) {
        return response()->json(['error' => 'Forbidden'], 403);
    }

    $data = $request->validate([
        'status' => 'required|string|in:Not set,Urgent,In-Progress,Done',
    ]);

    $task->status = $data['status'];
    $task->save();

    return response()->json($task);
}

}
