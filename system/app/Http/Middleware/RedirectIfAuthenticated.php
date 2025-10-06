<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     * If user is authenticated, redirect to the appropriate dashboard by role.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        if (Auth::check()) {
            $role = Auth::user()->role ?? null;
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            if ($role === 'staff') {
                return redirect()->route('staff.dashboard');
            }
            if ($role === 'patient') {
                return redirect()->route('patient.dashboard');
            }
            return redirect('/');
        }

        return $next($request);
    }
}
