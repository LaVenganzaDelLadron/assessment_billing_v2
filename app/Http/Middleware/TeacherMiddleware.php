<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
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

        if (auth()->user()->role !== 'teacher') {
            return response([
                'message' => 'Forbidden - Teacher access required',
            ], 403);
        }

        return $next($request);
    }
}
