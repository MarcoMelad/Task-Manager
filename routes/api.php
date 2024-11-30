<?php

use App\Http\Controllers\Task\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'tasks'], function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::get('/trashed', [TaskController::class, 'trashedTasks']);
        Route::post('/add', [TaskController::class, 'store']);
        Route::get('/{task}', [TaskController::class, 'show']);
        Route::post('edit/{task}', [TaskController::class, 'update']);
        Route::post('/{task}/assign', [TaskController::class, 'assignTask']);
        Route::get('/{user}/user-tasks', [TaskController::class, 'getUserTasks']);
        Route::post('delete/{task}', [TaskController::class, 'destroy']);
        Route::post('retrieve/{task}', [TaskController::class, 'retrieveTasks']);
        Route::post('force-delete/{task}', [TaskController::class, 'forceDelete']);
    });
});
