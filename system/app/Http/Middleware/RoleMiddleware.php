<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Accepts a single role (e.g. 'admin') or comma-separated list (e.g. 'admin,staff').
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $allowed = array_map('trim', explode(',', $roles));
        if (!in_array(Auth::user()->role, $allowed, true)) {
            // Optionally could return 403, but redirecting to login keeps prior behavior style
            return redirect()->route('login');
        }

        return $next($request);
    }
}
