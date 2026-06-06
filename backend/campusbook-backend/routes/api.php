<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoomController;


/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
 */

// Route::apiResource('projects', ProjectController::class);
Route::apiResource('reservations', ReservationController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('rooms', RoomController::class);