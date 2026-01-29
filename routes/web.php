<?php

use App\Http\Controllers\Auth\BridgeLoginController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect(config('app.frontend_url') . '/login');
})->name('login');

Route::get('/auth/bridge', [BridgeLoginController::class, 'bridge'])->name('auth.bridge');
