@extends('layouts.app')

@section('title', 'My Tasks - Task Manager')

@section('content')
    <div class="page-header">
        <div class="page-heading">
            <h1>My Tasks</h1>
            <p>Here's what you need to get done.</p>
        </div>
        <button type="button" class="btn-primary">New Task</button>
    </div>

    <div class="empty-state">
        <p>No tasks yet.</p>
    </div>
@endsection
