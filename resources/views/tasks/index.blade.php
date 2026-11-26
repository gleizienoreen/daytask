@extends('layouts.app')

@section('title', 'My Tasks - Task Manager')

@section('content')
    <div id="tasks-dashboard">
        <div class="page-header">
            <div class="page-heading">
                <h1>My Tasks</h1>
                <p>Manage and track your tasks to stay productive.</p>
            </div>
            <x-button :href="route('tasks.create')">New Task</x-button>
        </div>

        <x-alert id="dashboard-alert" class="hidden" />

        <div class="filters-bar">
            <input
                type="text"
                id="search-input"
                class="form-input filter-search"
                placeholder="Search tasks by title..."
            >
            <select id="category-filter" class="form-select filter-category">
                <option value="">All Categories</option>
            </select>
        </div>

        <div id="tasks-loading" class="empty-state">
            <p>Loading tasks...</p>
        </div>

        <div id="tasks-empty" class="empty-state hidden">
            <p id="tasks-empty-text">No tasks yet. Click "New Task" to create one.</p>
        </div>

        <x-table
            id="tasks-table-wrapper"
            class="hidden"
            :headers="['Title', 'Category', 'Status', 'Due Date', 'Actions']"
            body-id="tasks-table-body"
        />

        <div id="pagination-controls" class="pagination hidden">
            <x-button variant="secondary" id="prev-page-btn">Previous</x-button>
            <span id="pagination-info" class="pagination-info"></span>
            <x-button variant="secondary" id="next-page-btn">Next</x-button>
        </div>

        <div id="delete-modal" class="modal-overlay hidden" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
            <div class="modal-dialog">
                <h2 id="delete-modal-title" class="modal-title">Delete Task</h2>
                <p id="delete-modal-message" class="modal-message">
                    Are you sure you want to delete this task? This action cannot be undone.
                </p>
                <div class="modal-actions">
                    <x-button variant="secondary" id="cancel-delete-btn">Cancel</x-button>
                    <x-button id="confirm-delete-btn">Delete</x-button>
                </div>
            </div>
        </div>
    </div>
@endsection
