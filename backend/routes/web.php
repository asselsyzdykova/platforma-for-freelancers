<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SubscriptionController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/stripe/webhook', [SubscriptionController::class, 'handleWebhook']);
