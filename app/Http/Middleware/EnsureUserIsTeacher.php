<?php

/**
 * Allows only faculty to access a route.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTeacher
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isTeacher() || ! $user->teacher) {
            abort(403, 'Access denied. A valid teacher profile is required.');
        }

        return $next($request);
    }
}
