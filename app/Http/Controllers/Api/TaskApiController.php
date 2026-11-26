<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with('category')
            ->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . $search . '%']);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $tasks = $query->orderBy('due_date', 'asc')->paginate(5);

        return response()->json($tasks);
    }

    public function show($id)
    {
        $task = Task::with('category')->findOrFail($id);

        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json($task);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:pending,done',
            'due_date' => 'required|date',
        ], [], [
            'category_id' => 'category',
        ]);

        $task = $request->user()->tasks()->create($validated);
        $task->load('category');

        return response()->json([
            'message' => 'Task created successfully.',
            'task' => $task,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:pending,done',
            'due_date' => 'required|date',
        ], [], [
            'category_id' => 'category',
        ]);

        $task->update($validated);
        $task->load('category');

        return response()->json([
            'message' => 'Task updated successfully.',
            'task' => $task,
        ]);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully.',
        ]);
    }
}
