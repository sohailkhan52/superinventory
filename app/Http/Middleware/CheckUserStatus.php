<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check User Status Middleware
 * 
 * Verifies that authenticated users have active status on every request.
 * This prevents inactive/suspended users from continuing sessions.
 * 
 * Applied to all admin routes to ensure real-time status enforcement.
 */
class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is authenticated, check their status
        if ($user) {
            // Super admin bypass - they can always access
            if ($user->isSuperAdmin()) {
                return $next($request);
            }

            // User status is not approved - log them out immediately
            if (!$user->isApproved()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                $message = match ($user->status) {
                    'pending' => 'Your account is pending approval. Please contact your administrator.',
                    'rejected' => 'Your account registration was rejected.',
                    'suspended' => 'Your account has been suspended.',
                    default => 'Your account is not active.',
                };
                
                return redirect()->route('login')
                    ->with('error', $message);
            }
        }

        return $next($request);
    }
}
