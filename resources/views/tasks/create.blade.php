@extends('layouts.app')

@section('title', 'New Task - Task Manager')

@section('content')
    <div class="page-header">
        <div class="page-heading">
            <h1>New Task</h1>
            <p>Fill in the details to create a new task.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf

            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title') }}" 
                    class="form-input" 
                    required 
                    maxlength="255"
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description (optional)</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    class="form-textarea"
                >{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="category_id" class="form-label">Category</label>
                <select id="category_id" name="category_id" class="form-select" required>
                    <option value="">Select a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select" required>
                    <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="done" {{ old('status') === 'done' ? 'selected' : '' }}>Done</option>
                </select>
            </div>

            <div class="form-group">
                <label for="due_date" class="form-label">Due Date</label>
                <input 
                    type="date" 
                    id="due_date" 
                    name="due_date" 
                    value="{{ old('due_date') }}" 
                    class="form-input" 
                    required
                >
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Save Task</button>
                <a href="{{ route('tasks.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
