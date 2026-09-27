<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FrontendController;
use App\Http\Controllers\Api\ChatbotController;

Route::group(['namespace' => 'Api', 'prefix' => 'v1', 'middleware' => 'api'], function () {
    Route::get('app-config', [FrontendController::class, 'appconfig']);
    Route::get('slider', [FrontendController::class, 'slider']);
    Route::get('category-menu', [FrontendController::class, 'categorymenu']);
    Route::get('hotdeal-product', [FrontendController::class, 'hotdealproduct']);
    Route::get('homepage-product', [FrontendController::class, 'homepageproduct']);
    Route::get('footer-menu-left', [FrontendController::class, 'footermenuleft']);
    Route::get('footer-menu-right', [FrontendController::class, 'footermenuright']);
    Route::get('social-media', [FrontendController::class, 'socialmedia']);
    Route::get('contactinfo', [FrontendController::class, 'contactinfo']);
    Route::get('category/{id}', [FrontendController::class, 'catproduct']);
});

// Chatbot API Routes
Route::group(['middleware' => ['api', 'throttle:60,1']], function () {
    foreach (['v1/chat', 'chat'] as $prefix) {
        Route::prefix($prefix)->group(function () {
            Route::post('/conversations', [ChatbotController::class, 'createConversation']);
            Route::get('/conversations/current', [ChatbotController::class, 'getCurrentConversation']);
            Route::get('/conversations/{conversation}', [ChatbotController::class, 'showConversation']);
            Route::get('/conversations/{conversation}/messages', [ChatbotController::class, 'getMessages']);
            Route::post('/conversations/{conversation}/read', [ChatbotController::class, 'markAsRead']);
            Route::post('/conversations/{conversation}/close', [ChatbotController::class, 'closeConversation']);

            // Send message endpoint with customer/guest rate limiting (throttle:chat-messages)
            Route::middleware('throttle:chat-messages')->post('/conversations/{conversation}/messages', [ChatbotController::class, 'sendMessage']);
        });
    }
});

// Telegram Bot Webhook Endpoints
Route::post('telegram/webhook', [\App\Http\Controllers\Api\TelegramWebhookController::class, 'handle']);
Route::post('v1/telegram/webhook', [\App\Http\Controllers\Api\TelegramWebhookController::class, 'handle']);
Route::match(['get', 'post'], 'telegram/setup-webhook', [\App\Http\Controllers\Api\TelegramWebhookController::class, 'setupWebhook']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
