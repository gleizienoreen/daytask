@extends('layouts.app')

@section('title', 'My Tasks - Task Manager')

@section('content')
    <div class="page-header">
        <div class="page-heading">
            <h1>My Tasks</h1>
            <p>Manage and track your tasks to stay productive.</p>
        </div>
        <a href="{{ route('tasks.create') }}" class="btn-primary">New Task</a>
    </div>

    @if (session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($tasks->isEmpty())
        <div class="empty-state">
            <p>No tasks yet. Click "New Task" to create one.</p>
        </div>
    @else
        <div class="table-container">
            <table class="task-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td>
                                <div class="task-title">{{ $task->title }}</div>
                                @if ($task->description)
                                    <div class="task-desc">{{ $task->description }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-category">{{ $task->category->name }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $task->status }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn-link">Edit</a>
                                    <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
