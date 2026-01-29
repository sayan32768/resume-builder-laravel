<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ResumeController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login',    [UserController::class, 'login']);
    // Route::get('/verify/{id}/{hash}', [UserController::class, 'verify'])
    //     ->name('verification.verify');

    // Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
    // Route::post('/reset-password', [UserController::class, 'resetPassword']);
});

Route::middleware('auth:sanctum', 'updateLastSeen', 'not_blocked')->group(function () {

    Route::post('/auth/logout', [UserController::class, 'logout']);
    Route::get('/auth/me', [UserController::class, 'me']);
    Route::post('/auth/refresh', [UserController::class, 'refresh']);
    // Route::post('/auth/email/resend', [UserController::class, 'resendVerification']);

    Route::get('/resume/all', [ResumeController::class, 'getPastResumes']);
    Route::post('/resume/create', [ResumeController::class, 'create']);
    Route::put('/resume/{id}', [ResumeController::class, 'update']);
    Route::delete('/resume/{id}', [ResumeController::class, 'destroy']);
    Route::get('/resume/{id}', [ResumeController::class, 'show']);
});
