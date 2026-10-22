<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user) {
            throw new HttpException(403, 'Unauthorized access');
        }

        if (!in_array($user->role, $roles)) {
            throw new HttpException(403, 'Access denied. You do not have the required role.');
        }

        return $next($request);
    }
}