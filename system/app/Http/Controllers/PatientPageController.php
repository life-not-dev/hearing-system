<?php

namespace App\Http\Controllers;

class PatientPageController extends Controller
{
	public function home()
	{
		return view('patient.patient-home');
	}

	public function dashboard()
	{
		return view('patient.patient-dashboard');
	}

	public function testResult()
	{
		return view('patient.patient-view-testresult');
	}

	public function appointment()
	{
		return view('patient.patient-appointment');
	}

	public function message()
	{
		return view('patient.patient-message');
	}
}


