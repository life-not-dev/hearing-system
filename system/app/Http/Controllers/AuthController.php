<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form for specified role
     */
    public function showLoginForm($role = null)
    {
        // If already authenticated, redirect to the correct dashboard
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            if ($user->role === 'staff') {
                return redirect()->route('staff.dashboard');
            }
            if ($user->role === 'patient') {
                return redirect()->route('patient.dashboard');
            }
            // Unknown role: send to home
            return redirect('/');
        }

        // Prefer new wrapper path; fallback to legacy flat blade if wrapper not yet created
        if (view()->exists('admin.auth.login')) {
            return view('admin.auth.login', compact('role'));
        }
        // Fallback to new admin folder login if flat not present
        if (view()->exists('admin.admin-login')) {
            return view('admin.admin-login', compact('role'));
        }
        return view('admin-login', compact('role'));
    }

    /**
     * Authenticate user with specified role
     */
    public function login(Request $request, $role = null)
    {
        // Backend validation for email
        $request->validate([
            'email' => [
                'required',
                'email',
                'not_regex:/[<>;=\'\,]/', // Forbid < > ; = ' ,
            ],
            'password' => [
                'required',
                'min:8',
                'regex:/[A-Z]/', // at least one uppercase
                'regex:/[a-z]/', // at least one lowercase
                'regex:/[0-9]/', // at least one number
                'regex:/[^A-Za-z0-9]/', // at least one special character
            ],
        ], [
            'email.email' => 'Email must be a valid email address.',
            'email.not_regex' => "Email must not contain special characters like <, >, ;, =, ', or ,.",
            'password.min' => 'Password must be at least 8 characters.',
            'password.regex' => 'Password must include 1 uppercase, 1 lowercase, 1 number, and 1 special character (e.g., Test@123).',
        ]);

        $credentials = $request->only('email','password');

        // If specific role provided, try only that role; otherwise try all roles
        $rolesToTry = $role ? [$role] : ['admin','staff'];

        $emailExists = User::where('email', $credentials['email'])
            ->when($role, function($query) use ($role) {
                $query->where('role', $role);
            })
            ->exists();

        foreach ($rolesToTry as $userRole) {
            if (Auth::attempt(array_merge($credentials,['role'=>$userRole]), $request->boolean('remember'))) {
                $request->session()->regenerate();
                $successMsg = 'You have successfully logged in!';
                $redirect = $userRole === 'admin' ? route('admin.dashboard') : route('staff.dashboard');
                if ($request->expectsJson()) {
                    return response()->json(['ok' => true, 'message' => $successMsg, 'redirect' => $redirect]);
                }
                return redirect()->to($redirect)->with('success', $successMsg);
            }
        }

        if (!$emailExists) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'Incorrect Email Address'], 422);
            }
            return back()->with('error', 'Incorrect Email Address');
        }

        $msg = 'Incorrect credentials. Double-check your email and password before login';
        if ($request->expectsJson()) {
            return response()->json(['ok' => false, 'message' => $msg], 401);
        }
        return back()->with('error', $msg);
    }

    /**
     * Logout user and redirect to login
     */
    public function logout(Request $request, $role = null)
    {
        // If logging out a patient session specifically, use the patient guard
        if ($role === 'patient') {
            try { \Illuminate\Support\Facades\Auth::guard('patient')->logout(); } catch (\Throwable $e) {}
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('patient.login');
        }

        // Default: logout the primary guard
        Auth::logout();
        // Also try to clear patient guard if present to avoid mixed sessions
        try { \Illuminate\Support\Facades\Auth::guard('patient')->logout(); } catch (\Throwable $e) {}
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

}