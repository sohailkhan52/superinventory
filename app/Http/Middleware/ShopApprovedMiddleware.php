<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShopApprovedMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin bypasses all checks
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Shop user: must have shop_id
        if (empty($user->shop_id)) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account is not linked to any shop.');
        }

        // Shop user: must have active status
        if ($user->status !== 'active') {
            $message = match ($user->status) {
                'inactive'  => 'Your account has been deactivated. Please contact support.',
                'suspended' => 'Your account has been suspended. Please contact support.',
                default     => 'Your account is not active.',
            };

            auth()->logout();
            return redirect()->route('login')->with('error', $message);
        }

        // Check the shop itself
        if ($user->shop && $user->shop->status !== 'approved') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your shop is not active. Status: ' . $user->shop->status);
        }

        return $next($request);
    }
}
