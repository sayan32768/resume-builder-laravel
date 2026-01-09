<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\UserController;

Route::get('/resume/all', [ResumeController::class, 'getPastResumes']);
// Route::get('/resumes', [ResumeController::class, 'index']);
Route::post('/resume/create', [ResumeController::class, 'create']);
Route::put('/resume/{id}', [ResumeController::class, 'update']);
Route::delete('/resume/{id}', [ResumeController::class, 'destroy']);
Route::get('/resume/{id}', [ResumeController::class, 'show']);
