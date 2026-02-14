<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

class BridgeLoginController extends Controller
{
    // public function bridge(Request $request)
    // {
    //     $token = $request->query('token');

    //     $accessToken = PersonalAccessToken::findToken($token);
    //     abort_if(!$accessToken, 401);

    //     $user = $accessToken->tokenable;
    //     abort_if(!$user, 401);

    //     abort_if($user->role !== 'ADMIN', 403);

    //     Auth::login($user);
    //     $request->session()->regenerate();

    //     AuditLogger::log('ADMIN_LOGIN_SUCCESS', $user);

    //     return redirect()->route('admin.dashboard');
    // }

    public function bridge(Request $request)
    {
        $token = $request->query('token');

        // -------- TOKEN NOT FOUND --------
        if (!$token) {
            AuditLogger::log(
                'ADMIN_BRIDGE_TOKEN_MISSING',
                null,
                null,
                null,
                ['reason' => 'token_not_provided']
            );

            abort(401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            AuditLogger::log(
                'ADMIN_BRIDGE_TOKEN_INVALID',
                null,
                null,
                null,
                ['reason' => 'token_not_found']
            );

            abort(401);
        }

        $user = $accessToken->tokenable;

        if (!$user) {
            AuditLogger::log(
                'ADMIN_BRIDGE_USER_NOT_FOUND',
                null,
                null,
                null,
                [
                    'token_id' => $accessToken->id,
                ]
            );

            abort(401);
        }

        // -------- NOT ADMIN --------
        if ($user->role !== 'ADMIN') {
            AuditLogger::log(
                'ADMIN_BRIDGE_FORBIDDEN',
                $user,
                null,
                null,
                [
                    'token_id' => $accessToken->id,
                    'user_role' => $user->role,
                ],
                $user->id
            );

            abort(403);
        }

        // -------- SUCCESSFUL ADMIN LOGIN --------
        Auth::login($user);
        $request->session()->regenerate();

        AuditLogger::log(
            'ADMIN_LOGIN_SUCCESS',
            $user,
            null,
            null,
            [
                'method' => 'bridge',
                'token_id' => $accessToken->id,
            ]
        );

        return redirect()->route('admin.dashboard');
    }
}
