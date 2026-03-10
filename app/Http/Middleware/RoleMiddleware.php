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

        if (!in_array(auth()->user()->role, $roles)) {
            // Redirect to appropriate dashboard based on user's actual role
            return match (auth()->user()->role) {
                'shop' => redirect()->route('shop.dashboard'),
                'customer' => redirect()->route('customer.dashboard'),
                'system_owner' => redirect()->route('admin.dashboard'),
                default => redirect()->route('login'),
            };
        }

        return $next($request);
    }
}
