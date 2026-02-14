<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminRequestController;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});


Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('tasks',TaskController::class);
    Route::apiResource('categories',CategoryController::class);
});

Route::post('/admin-requests/delete-task', [AdminRequestController::class, 'requestdeleteTask'])->middleware('auth:sanctum');
