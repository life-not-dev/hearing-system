<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon; // optional

class PatientRecordController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'birthday' => 'required|date',
            'gender' => 'required|string|max:20',
            'patient_type' => 'nullable|string|max:50', // not saved yet (no column) but kept for future
            'date_registered' => 'required|date',
        ]);

        try {
            return DB::transaction(function() use ($data) {
                $patient = Patient::create([
                    'patient_firstname' => $data['first_name'],
                    'patient_middlename' => $data['middle_name'] ?? null,
                    'patient_surname' => $data['last_name'],
                    'patient_birthdate' => $data['birthday'],
                    'patient_gender' => $data['gender'],
                ]);

                $record = PatientRecord::create([
                    'patient_id' => $patient->patient_id,
                    'patient_record_date_registered' => $data['date_registered'],
                ]);

                // Determine redirect route based on the current route
                $redirectRoute = request()->routeIs('admin.*') ? 'admin.patient.record' : 'staff.patient.record';
                
                return redirect()
                    ->route($redirectRoute)
                    ->with('success', 'Patient record created (ID: '. $record->patient_record_id .').');
            });
        } catch (\Throwable $e) {
            Log::error('Failed to create patient record', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to save patient record.');
        }
    }
}
