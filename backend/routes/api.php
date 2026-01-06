<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FreelancerProfileController;
use App\Http\Controllers\Api\FreelancerListController;
use App\Http\Controllers\Api\ClientProfileController;
use App\Http\Controllers\Api\ClientProjectController;
use App\Http\Controllers\Api\ProjectController;

use App\Http\Controllers\Api\NotificationController;

use App\Http\Controllers\Api\ProposalController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/projects/{id}/apply', [ProposalController::class, 'apply']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/client/notifications', [NotificationController::class, 'index']);
    Route::post('/client/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications', [NotificationController::class, 'store']);
});


Route::middleware('auth:sanctum')->get('/projects', [ProjectController::class, 'index']);
Route::middleware('auth:sanctum')->delete('/client/projects/{project}', [ProjectController::class, 'destroy']);

Route::get('/freelancers', [FreelancerListController::class, 'index']);

Route::middleware('auth:sanctum')
->get('/me', [UserController::class, 'me']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/client/profile', [ClientProfileController::class, 'show']);
    Route::post('/client/profile', [ClientProfileController::class, 'update']);
    Route::post('/client/projects', [ClientProjectController::class, 'store']);
    Route::get('/client/projects', [ClientProjectController::class, 'index']);
});


Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/freelancer/profile', [FreelancerProfileController::class, 'show']);
    Route::post('/freelancer/profile', [FreelancerProfileController::class, 'update']);
});
