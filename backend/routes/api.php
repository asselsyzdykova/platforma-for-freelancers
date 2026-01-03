<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FreelancerProfileController;

Route::middleware('auth:sanctum')
->get('/me', [UserController::class, 'me']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/freelancer/profile', [FreelancerProfileController::class, 'show']);
    Route::post('/freelancer/profile', [FreelancerProfileController::class, 'update']);
});
