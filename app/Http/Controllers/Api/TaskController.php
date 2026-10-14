<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    /**
     * List the authenticated user's tasks.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $tasks = $user->tasks()
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Tasks retrieved successfully.',
            'data' => [
                'tasks' => $tasks,
                'username' => $user->name,
            ],
        ]);
    }

    /**
     * Create a new task.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['required', 'date'],
        ]);

        $task = $request->user()
            ->tasks()
            ->create($validated);

        return response()->json([
            'message' => 'Task created successfully.',
            'data' => [
                'task' => $task,
            ],
        ], 201);
    }

    /**
     * Display a specific task.
     */
    public function show(Request $request, int $task): JsonResponse
    {
        $task = $request->user()
            ->tasks()
            ->findOrFail($task);

        return response()->json([
            'message' => 'Task retrieved successfully.',
            'data' => [
                'task' => $task,
            ],
        ]);
    }

    /**
     * Toggle task completion.
     */
    public function toggleCompletion(
        Request $request,
        int $task
    ): JsonResponse {
        $task = $request->user()
            ->tasks()
            ->findOrFail($task);

        if ($task->completed_at !== null) {
            $task->completed_at = null;
        } else {
            $task->completed_at = now();
        }

        $task->save();

        return response()->json([
            'message' => 'Task completion updated successfully.',
            'data' => [
                'task' => $task->fresh(),
            ],
        ]);
    }

    /**
     * Update an existing task.
     */
    public function update(
        Request $request,
        int $task
    ): JsonResponse {
        $task = $request->user()
            ->tasks()
            ->findOrFail($task);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['required', 'date'],
        ]);

        $task->update($validated);

        return response()->json([
            'message' => 'Task updated successfully.',
            'data' => [
                'task' => $task->fresh(),
            ],
        ]);
    }

    /**
     * Delete a task.
     */
    public function destroy(
        Request $request,
        int $task
    ): JsonResponse {
        $task = $request->user()
            ->tasks()
            ->findOrFail($task);

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully.',
        ]);
    }
}

