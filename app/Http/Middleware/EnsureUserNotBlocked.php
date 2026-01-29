<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserNotBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // not logged in? let auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // if blocked
        if ($user->is_blocked) {
            // API request → JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your account is blocked.',
                ], 403);
            }

            // web request → redirect
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account is blocked.');
        }

        return $next($request);
    }
}
