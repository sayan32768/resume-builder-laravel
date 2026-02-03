<?php

use App\Http\Controllers\Admin\AuditLogExportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LogoutController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ResumeDataController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        Route::view('/dashboard', 'admin.dashboard.index')
            ->name('admin.dashboard');

        Route::view('/analytics', 'admin.analytics.index')
            ->name('admin.analytics.index');

        Route::get('/reports/export', [ReportController::class, 'exportCsv'])
            ->name('admin.reports.export');

        Route::get('/audit-logs/export', [AuditLogExportController::class, 'exportCsv'])
            ->name('admin.audit-logs.export');

        Route::view('/users', 'admin.users.index')->name('admin.users.index');

        Route::view('/resumes', 'admin.resumes.index')->name('admin.resumes.index');

        Route::get('/resumes/{resume}/show', [ResumeDataController::class, 'show'])
            ->name('admin.resumes.show');

        Route::get('/resumes/{resume}/preview', [ResumeDataController::class, 'preview'])
            ->name('admin.resumes.preview');

        Route::view('/templates', 'admin.templates.index')
            ->name('admin.templates.index');

        Route::view('/audit-logs', 'admin.audit-logs.index')
            ->name('admin.audit-logs.index');

        Route::get('/users/{user}', [UserController::class, 'show'])
            ->name('admin.users.show');
    });

Route::prefix('admin')
    ->middleware(['auth'])
    ->group(function () {
        Route::post('/logout', [LogoutController::class, 'logout'])->name('admin.logout');
    });
