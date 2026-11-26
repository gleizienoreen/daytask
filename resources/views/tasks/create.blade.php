@extends('layouts.app')

@section('title', 'New Task - Task Manager')

@section('content')
    <div class="page-header">
        <div class="page-heading">
            <h1>New Task</h1>
            <p>Fill in the details to create a new task.</p>
        </div>
    </div>

    <x-alert id="form-alert" class="hidden" />

    <div class="form-container">
        <form id="task-create-form">
            <x-input
                label="Title"
                name="title"
                required
                maxlength="255"
                autofocus
            />

            <div class="form-group">
                <label for="description" class="form-label">Description (optional)</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="form-textarea"
                ></textarea>
                <p class="field-error hidden" data-error-for="description"></p>
            </div>

            <div class="form-group">
                <label for="category_id" class="form-label">Category</label>
                <select id="category_id" name="category_id" class="form-select" required>
                    <option value="">Select a category</option>
                </select>
                <p class="field-error hidden" data-error-for="category_id"></p>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select" required>
                    <option value="pending" selected>Pending</option>
                    <option value="done">Done</option>
                </select>
                <p class="field-error hidden" data-error-for="status"></p>
            </div>

            <x-input
                label="Due Date"
                name="due_date"
                type="date"
                required
            />

            <div class="form-actions">
                <x-button type="submit" id="submit-btn">Save Task</x-button>
                <x-button variant="secondary" :href="route('tasks.index')">Cancel</x-button>
            </div>
        </form>
    </div>
@endsection
