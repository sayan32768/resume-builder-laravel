<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LogoutController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');

        Route::view('/users', 'admin.users.index')->name('admin.users');
        Route::view('/resumes', 'admin.resumes.index')->name('admin.resumes');
    });

Route::prefix('admin')
    ->middleware(['auth'])
    ->group(function () {
        Route::post('/logout', [LogoutController::class, 'logout'])->name('admin.logout');
    });
