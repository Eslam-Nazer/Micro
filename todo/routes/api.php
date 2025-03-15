<?php

use App\Http\Controllers\TodoListController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('user/login', [UserController::class, 'login'])->name('user.login');
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('todo-list')->group(function () {
        Route::get('/', [TodoListController::class, 'index']);
        Route::post('/', [TodoListController::class, 'create']);
        Route::get('show-queue', [TodoListController::class, 'showQueue']);
    });
});
