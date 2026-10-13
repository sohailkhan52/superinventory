<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user) {
            abort(403, 'User not authenticated.');
        }

        // Super admin has all roles implicitly
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has any of the required roles using Spatie
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // User doesn't have required role
        abort(403, 'Unauthorized. You do not have the required role: ' . implode(', ', $roles));
    }
}
