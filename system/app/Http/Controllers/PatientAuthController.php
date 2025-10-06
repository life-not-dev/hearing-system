<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientAuthController extends Controller
{
	public function showLogin()
	{
		if (Auth::guard('patient')->check() && Auth::guard('patient')->user()->role === 'patient') {
			return redirect()->route('patient.dashboard');
		}
		return view('patient.patient-login');
	}

	public function login(Request $req)
	{
		$credentials = $req->only('email','password');
		$remember = (bool) $req->boolean('remember');
		if (Auth::guard('patient')->attempt($credentials, $remember)) {
			if (Auth::guard('patient')->user()->role === 'patient') {
				$req->session()->regenerate();
				return redirect()->route('patient.dashboard');
			}
			Auth::guard('patient')->logout();
			return back()->withErrors(['email' => 'Account is not a patient account.'])->withInput($req->except('password'));
		}
		return back()->withErrors(['email' => 'Invalid credentials.'])->withInput($req->except('password'));
	}

	public function logout(Request $req)
	{
		try { Auth::guard('patient')->logout(); } catch (\Throwable $e) {}
		return redirect()->route('patient.login');
	}
}


