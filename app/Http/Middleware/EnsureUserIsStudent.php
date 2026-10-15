<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isStudent() || ! $user->student) {
            abort(403, 'Access denied. A valid student profile is required.');
        }

        return $next($request);
    }
}
