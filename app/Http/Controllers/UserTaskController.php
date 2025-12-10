<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserTaskController extends Controller
{
    public function index()
    {
        $tasks = \App\Models\UserTask::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        $task = \App\Models\UserTask::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'title' => $request->title,
            'due_date' => $request->due_date,
            'is_completed' => false,
        ]);

        return response()->json($task, 201);
    }

    public function update(Request $request, $id)
    {
        $task = \App\Models\UserTask::where('user_id', \Illuminate\Support\Facades\Auth::id())->findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'is_completed' => 'sometimes|boolean',
            'due_date' => 'nullable|date',
        ]);

        $task->update($request->all());

        return response()->json($task);
    }

    public function destroy($id)
    {
        $task = \App\Models\UserTask::where('user_id', \Illuminate\Support\Facades\Auth::id())->findOrFail($id);
        $task->delete();

        return response()->json(null, 204);
    }
}
