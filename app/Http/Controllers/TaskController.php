<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    public function index(): Response
    {
        $user= auth()->user();
        $tasks=$user
            ->tasks()
            ->latest()
            ->get();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'username'=>$user->name,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['required', 'date', 'after:now'],
        ]);

        $request->user()->tasks()->create($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Task created successfully.');
    }
    public function toggleCompletion(Request $request, int $task):RedirectResponse{
        $task=$request->user()
            ->tasks()
            ->findOrFail($task);
        
        if($task->completed_at !== null){
            $task->completed_at=null;
        }else{
            $task->completed_at=now();
        }
        $task->save();

        return redirect()
            ->route('dashboard')
            ->with('success','Task completion updated.');
    }
    public function update(Request $request, int $task): RedirectResponse{
        $task=$request->user()
            ->tasks()
            ->findOrFail($task);

        $validated=$request->validate([
            'title'=>['required','string','max:255'],
            'description'=>['nullable','string'],
            'due_at'=>['required','date'],
        ]);

        $task->update($validated);
        return redirect()
            ->route('dashboard')
            ->with('success','Task updated successfully.');
    }
    public function destroy(Request $request,int $task): RedirectResponse{
        $task=$request->user()
            ->tasks()
            ->findOrFail($task);

        $task->delete();
        return redirect()
            ->route('dashboard')
            ->with('success','Task deleted successfully.');
    }
}