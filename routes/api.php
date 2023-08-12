<?php

use App\Http\Controllers\PomoController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\ProjectTodoController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth Group to ApiAuthController
Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);


// Resources route to /api/projects
Route::middleware('auth:sanctum')->group( function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/me', [ApiAuthController::class, 'me']);

    Route::apiResource('project', ProjectController::class);
    Route::apiResource('project.todo', ProjectTodoController::class);
    Route::apiResource('pomo', PomoController::class);
});
