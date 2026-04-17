<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return response([
                'message' => 'Unauthorized - Authentication required',
            ], 401);
        }

        if (auth()->user()->role !== 'student') {
            return response([
                'message' => 'Forbidden - Student access required',
            ], 403);
        }

        return $next($request);
    }
}
