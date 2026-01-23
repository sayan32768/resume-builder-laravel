<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'admin'])->get('/dashboard', function () {
    return view('welcome');
});

Route::get('/auth/bridge', function (Request $request) {
    $token = $request->query('token');

    $accessToken = PersonalAccessToken::findToken($token);
    abort_if(!$accessToken, 401);

    $user = $accessToken->tokenable;

    Auth::login($user);

    if ($user->role !== 'ADMIN') {
        abort(403);
    }

    return redirect('/dashboard');
});
