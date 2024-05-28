<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         if (auth()->check() && auth()->user()->role === User::ROLE_ADMIN) {
            return $next($request);
        }

        if (auth()->check() && auth()->user()->role === User::ROLE_SUPER_ADMIN) {
            return $next($request);
        }


        return response()->json(['message' => 'ليس لديك الصلاحية'], 403);
    }
}
