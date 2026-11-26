<?php

use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/categories', [CategoryApiController::class, 'index']);
    Route::get('/tasks', [TaskApiController::class, 'index']);
    Route::get('/tasks/{id}', [TaskApiController::class, 'show']);
    Route::post('/tasks', [TaskApiController::class, 'store']);
    Route::put('/tasks/{id}', [TaskApiController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskApiController::class, 'destroy']);
});
