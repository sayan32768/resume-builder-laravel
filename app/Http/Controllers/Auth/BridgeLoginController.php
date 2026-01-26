<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

class BridgeLoginController extends Controller
{
    public function bridge(Request $request)
    {
        $token = $request->query('token');

        $accessToken = PersonalAccessToken::findToken($token);
        abort_if(!$accessToken, 401);

        $user = $accessToken->tokenable;
        abort_if(!$user, 401);

        abort_if($user->role !== 'ADMIN', 403);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }
}
