<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);

        $user = $request->user();
        $token = $user?->currentAccessToken();

        if ($user && $token) {
            \App\Models\UserSession::where('access_token_id', $token->id)
                ->update(['last_seen_at' => now()]);
        }

        return $response;
    }
}
