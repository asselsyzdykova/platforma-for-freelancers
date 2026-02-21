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
use App\Http\Controllers\Api\FreelancerNotificationController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\ManagerController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\EmailVerificationController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/projects/{id}/apply', [ProposalController::class, 'apply']);

    Route::get('/client/applications/{id}', [ProposalController::class, 'show']);
    Route::post('/client/applications/{id}/view', [ProposalController::class, 'view']);
    Route::post('/client/applications/{id}/review', [ProposalController::class, 'review']);
    Route::post('/client/applications/{id}/accept', [ProposalController::class, 'accept']);
    Route::post('/client/applications/{id}/reject', [ProposalController::class, 'reject']);

    Route::get('/freelancer/proposals', [ProposalController::class, 'freelancerProposals']);

    Route::get('/client/notifications', [NotificationController::class, 'index']);
    Route::post('/client/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications', [NotificationController::class, 'store']);

    Route::get('/freelancer/notifications', [FreelancerNotificationController::class, 'index']);
    Route::post('/freelancer/notifications/{id}/read',[FreelancerNotificationController::class, 'markAsRead']);
    Route::get('/freelancer/notifications-unread-count',[FreelancerNotificationController::class, 'unreadCount']);


    Route::get('/client/profile', [ClientProfileController::class, 'show']);
    Route::post('/client/profile', [ClientProfileController::class, 'update']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/client/projects', [ClientProjectController::class, 'store']);
    Route::get('/client/projects', [ClientProjectController::class, 'index']);
    Route::get('/client/projects/{id}/proposals', [ProposalController::class, 'projectProposals']);



    Route::get('/freelancer/profile', [FreelancerProfileController::class, 'show']);
    Route::post('/freelancer/profile', [FreelancerProfileController::class, 'update']);
    Route::get('/skills', [FreelancerProfileController::class, 'allSkills']);
    Route::put('/freelancer/account', [FreelancerProfileController::class, 'update']);
    Route::put('/freelancer/account', [FreelancerProfileController::class, 'updateAccount']);
    Route::delete('/freelancer/profile', [FreelancerProfileController::class, 'destroy']);

    //subscriptions
    Route::middleware('auth:sanctum')->post('/create-checkout-session', [SubscriptionController::class, 'createCheckoutSession']);
    Route::middleware('auth:sanctum')->post('/paypal/create-subscription', [SubscriptionController::class, 'createPaypalSubscription']);
    Route::middleware('auth:sanctum')->post('/subscriptions/confirm', [SubscriptionController::class, 'confirmCheckout']);
    Route::middleware('auth:sanctum')->post('/subscriptions/confirm-paypal', [SubscriptionController::class, 'confirmPaypalSubscription']);
    Route::post('/subscriptions/checkout', [SubscriptionController::class, 'checkout']);
    Route::get('/billing/transactions', [SubscriptionController::class, 'transactions']);
    Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancel']);

    //admin
    Route::get('/admin/stats', [AdminController::class, 'adminStats']);
    Route::get('/admin/managers', [ManagerController::class, 'index']);
    Route::post('/admin/managers', [ManagerController::class, 'store']);
    Route::delete('/admin/managers/{id}', [ManagerController::class, 'destroy']);
    Route::post('/admin/tasks', [TaskController::class, 'store']);

    //managers
    Route::get('/manager/dashboard', [ManagerController::class, 'dashboard']);
    Route::get('/manager/tasks', [TaskController::class, 'getManagerTasks']);
    Route::post('/manager/tasks', [TaskController::class, 'store']);
    Route::patch('/manager/tasks/{task}', [TaskController::class, 'update']);

    // Chat
    Route::get('/conversations', [ChatController::class, 'conversations']);
    Route::get('/messages/{userId}', [ChatController::class, 'index']);
    Route::post('/messages', [ChatController::class, 'store']);
    Route::post('/messages/{userId}/read', [ChatController::class, 'markAsRead']);
});


Route::middleware('auth:sanctum')->get('/projects', [ProjectController::class, 'index']);
Route::middleware('auth:sanctum')->delete('/client/projects/{project}', [ProjectController::class, 'destroy']);

Route::get('/freelancers', [FreelancerListController::class, 'index']);

Route::get('/cities', [CityController::class, 'index']);

Route::middleware('auth:sanctum')->get('/me', [UserController::class, 'me']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);




Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// stripe paypal webhook
Route::post('/stripe/webhook', [SubscriptionController::class, 'handleWebhook']);
Route::post('/paypal/webhook', [SubscriptionController::class, 'handlePaypalWebhook']);


//email
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, '__invoke'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
    ->middleware(['throttle:6,1'])
    ->name('verification.send');
