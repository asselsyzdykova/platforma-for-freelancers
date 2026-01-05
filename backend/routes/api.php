<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FreelancerProfileController;
use App\Http\Controllers\Api\FreelancerListController;
use App\Http\Controllers\Api\ClientProfileController;

Route::get('/freelancers', [FreelancerListController::class, 'index']);

Route::middleware('auth:sanctum')
->get('/me', [UserController::class, 'me']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/client/profile', [ClientProfileController::class, 'show']);
    Route::post('/client/profile', [ClientProfileController::class, 'update']);

       Route::get('/client/projects', fn () => response()->json([]));
});


Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/freelancer/profile', [FreelancerProfileController::class, 'show']);
    Route::post('/freelancer/profile', [FreelancerProfileController::class, 'update']);
});
