<?php

use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\AuthController;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function (): void {
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:8,1');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::apiResource('articles', ArticleController::class)->only(['index', 'show'])->whereNumber('article');

    Route::middleware(['auth:sanctum', EnsureUserIsActive::class])->group(function (): void {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::apiResource('articles', ArticleController::class)->except(['index', 'show'])->whereNumber('article');
    });
});
