<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrevoWebhookController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API REST pour les applications externes (BDC, Artemisiae, mobile, etc.)
| Toutes les routes sont préfixées par /api
|
*/

// Authentication endpoints (SSO)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Protected auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'updatePassword']);
    });

    // SSO verification for external apps (BDC, Artemisiae)
    Route::get('/verify', [AuthController::class, 'verify']);
});

// API v1 - Public endpoints
Route::prefix('v1')->group(function () {
    // Articles (Hub)
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{article:slug}', [ArticleController::class, 'show']);
    Route::get('/articles/category/{category}', [ArticleController::class, 'byCategory']);

    // Events
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/upcoming', [EventController::class, 'upcoming']);
    Route::get('/events/{event:slug}', [EventController::class, 'show']);

    // Journal (Revue scientifique)
    Route::prefix('journal')->group(function () {
        Route::get('/issues', [JournalController::class, 'issues']);
        Route::get('/issues/{issue:slug}', [JournalController::class, 'showIssue']);
        Route::get('/articles', [JournalController::class, 'articles']);
        Route::get('/articles/{submission}', [JournalController::class, 'showArticle']);
        Route::get('/search', [JournalController::class, 'search']);
    });

    // Protected journal endpoints
    Route::middleware('auth:sanctum')->prefix('journal')->group(function () {
        Route::get('/submissions/my-submissions', [JournalController::class, 'mySubmissions']);
        Route::post('/submissions', [JournalController::class, 'submitArticle']);
        Route::get('/submissions/{submission}', [JournalController::class, 'showSubmission']);
        Route::post('/submissions/{submission}/revision', [JournalController::class, 'submitRevision']);
    });
});

// Webhooks (no auth, but verified by signature)
Route::prefix('webhooks')->group(function () {
    Route::post('/helloasso', [WebhookController::class, 'helloasso']);
    Route::post('/brevo', [BrevoWebhookController::class, 'handle']);
});
