<?php

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

Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout']);
        Route::get('me', [\App\Http\Controllers\AuthController::class, 'me']);
    });

    Route::prefix('drones')->group(function () {
        Route::get('/', [\App\Http\Controllers\DroneController::class, 'index']);
        Route::get('/online', [\App\Http\Controllers\DroneController::class, 'online']);
        Route::get('/nearby', [\App\Http\Controllers\DroneController::class, 'nearby']);
        Route::get('/dangerous', [\App\Http\Controllers\DroneController::class, 'dangerous']);
        Route::get('/{serial}/flight-path', [\App\Http\Controllers\DroneController::class, 'flightPath']);

        Route::middleware('role:admin')->group(function () {
            Route::post('/{serial}/mark-safe', [\App\Http\Controllers\DroneController::class, 'markSafe']);
        });
    });
});
