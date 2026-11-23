<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::redirect('/', '/tasks');

Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
