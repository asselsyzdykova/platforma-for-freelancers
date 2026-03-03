<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && in_array($user->role, ['admin', 'manager'])) {
            return $next($request);
        }

        if ($user && $user->blocked) {
            return response()->json([
                'message' => 'Your account is blocked.'
            ], 403);
        }
        return $next($request);
    }
}
