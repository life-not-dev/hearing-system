<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Build a map of appointment_id => file-backed patient record id
     * to generate details links without altering existing routes.
     */
    protected function mapAppointmentsToFileRecordIds($appointments): array
    {
        $path = storage_path('app/patient_records.json');
        $rows = [];
        if (file_exists($path)) {
            $json = @file_get_contents($path);
            $rows = $json ? (json_decode($json, true) ?: []) : [];
        }
        // Track max id for new inserts
        $maxId = 0; foreach ($rows as $r0) { $maxId = max($maxId, (int)($r0['id'] ?? 0)); }
        $makeKey = function($first, $middle, $last, $bday) {
            $norm = fn($s) => mb_strtolower(trim((string)$s));
            return $norm($first).'|'.$norm($middle).'|'.$norm($last).'|'.trim((string)$bday);
        };
        $index = [];
        foreach ($rows as $r) {
            $key = $makeKey($r['first_name'] ?? '', $r['middle_name'] ?? '', $r['last_name'] ?? '', $r['birthday'] ?? '');
            $index[$key] = $r['id'] ?? null;
        }
        $map = [];
        foreach ($appointments as $a) {
            $p = $a->patient;
            if (!$p) continue;
            $first = $p->patient_firstname ?? '';
            $middle = $p->patient_middlename ?? '';
            $last = $p->patient_surname ?? '';
            $bday = '';
            if ($p->patient_birthdate) {
                if (is_string($p->patient_birthdate)) { $bday = substr($p->patient_birthdate, 0, 10); }
                elseif (method_exists($p->patient_birthdate, 'format')) { $bday = $p->patient_birthdate->format('Y-m-d'); }
            }
            $key = $makeKey($first, $middle, $last, $bday);
            $fid = $index[$key] ?? null;
            if ($fid) { $map[$a->id] = $fid; }
            // Removed auto-creation logic - patients only added explicitly via plus button
        }
        // No longer auto-saving since we removed auto-creation
        return $map;
    }
    /**
     * Resolve a branch_id from a branch name using tbl_branch, or null if not found.
     */
    protected function resolveBranchIdByName(?string $branchName): ?int
    {
        if (!$branchName) return null;
        if (!Schema::hasTable('tbl_branch')) return null;
        try {
            // 1) Direct match
            $bid = DB::table('tbl_branch')->where('branch_name', $branchName)->value('branch_id');
            if ($bid) return (int)$bid;

            // 2) Case-insensitive LIKE
            $bid = DB::table('tbl_branch')->whereRaw('LOWER(branch_name) = ?', [mb_strtolower(trim($branchName))])->value('branch_id');
            if ($bid) return (int)$bid;

            $like = '%'.mb_strtolower(trim($branchName)).'%';
            $bid = DB::table('tbl_branch')->whereRaw('LOWER(branch_name) LIKE ?', [$like])->value('branch_id');
            if ($bid) return (int)$bid;

            // 3) Normalize by removing common words: city, branch, clinic, center; map aliases (cdo=>cagayan)
            $norm = fn($s) => preg_replace('/\s+/', ' ', trim(mb_strtolower((string)$s)));
            $strip = function($s){
                $s = mb_strtolower($s);
                $s = str_replace(['city branch','clinic branch','medical center','medical centre','center','centre','branch','city','clinic'], ' ', $s);
                $s = preg_replace('/\s+/', ' ', trim($s));
                return $s;
            };
            $alias = function($s){
                $s = str_replace(['cdo','cagayan de oro','cagayan de oro city'], 'cagayan', $s);
                return $s;
            };
            $needle = $alias($strip($norm($branchName)));
            $rows = DB::table('tbl_branch')->select('branch_id','branch_name')->get();
            foreach ($rows as $r) {
                $cand = $alias($strip($norm($r->branch_name)));
                if ($cand === $needle) return (int)$r->branch_id;
            }
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }
    /**
     * Store a newly booked appointment from patient home page.
     */
    public function store(Request $request)
    {
        // If JSON body present without proper headers, attempt to decode and merge
        $raw = $request->getContent();
        if ($raw && is_string($raw)) {
            $maybe = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($maybe)) {
                // Merge only when typical fields are missing to avoid overriding form data
                if (!$request->has('firstname') && isset($maybe['firstname'])) $request->merge(['firstname' => $maybe['firstname']]);
                if (!$request->has('first_name') && isset($maybe['first_name'])) $request->merge(['first_name' => $maybe['first_name']]);
                if (!$request->has('surname') && isset($maybe['surname'])) $request->merge(['surname' => $maybe['surname']]);
                if (!$request->has('middle') && isset($maybe['middle'])) $request->merge(['middle' => $maybe['middle']]);
                if (!$request->has('middlename') && isset($maybe['middlename'])) $request->merge(['middlename' => $maybe['middlename']]);
                if (!$request->has('age') && isset($maybe['age'])) $request->merge(['age' => $maybe['age']]);
                if (!$request->has('address') && isset($maybe['address'])) $request->merge(['address' => $maybe['address']]);
                if (!$request->has('contact') && isset($maybe['contact'])) $request->merge(['contact' => $maybe['contact']]);
                if (!$request->has('email') && isset($maybe['email'])) $request->merge(['email' => $maybe['email']]);
                if (!$request->has('gender') && isset($maybe['gender'])) $request->merge(['gender' => $maybe['gender']]);
                if (!$request->has('services') && isset($maybe['services'])) $request->merge(['services' => $maybe['services']]);
                if (!$request->has('service') && isset($maybe['service'])) $request->merge(['service' => $maybe['service']]);
                if (!$request->has('patient_type') && isset($maybe['patient_type'])) $request->merge(['patient_type' => $maybe['patient_type']]);
                if (!$request->has('branch') && isset($maybe['branch'])) $request->merge(['branch' => $maybe['branch']]);
                if (!$request->has('appointment_date') && isset($maybe['appointment_date'])) $request->merge(['appointment_date' => $maybe['appointment_date']]);
                if (!$request->has('appointment_time') && isset($maybe['appointment_time'])) $request->merge(['appointment_time' => $maybe['appointment_time']]);
                if (!$request->has('birthdate') && isset($maybe['birthdate'])) $request->merge(['birthdate' => $maybe['birthdate']]);
                if (!$request->has('birth_day') && isset($maybe['birth_day'])) $request->merge(['birth_day' => $maybe['birth_day']]);
                if (!$request->has('birth_month') && isset($maybe['birth_month'])) $request->merge(['birth_month' => $maybe['birth_month']]);
                if (!$request->has('birth_year') && isset($maybe['birth_year'])) $request->merge(['birth_year' => $maybe['birth_year']]);
            }
        }
        // Accept either 'firstname' or legacy 'first_name' from existing form
        if ($request->filled('first_name') && !$request->filled('firstname')) {
            $request->merge(['firstname' => $request->input('first_name')]);
        }

        // Map 'middle' => 'middlename' if provided
        if ($request->filled('middle') && !$request->filled('middlename')) {
            $request->merge(['middlename' => $request->input('middle')]);
        }

        // Map 'service' => 'services' if provided
        if ($request->filled('service') && !$request->filled('services')) {
            $request->merge(['services' => $request->input('service')]);
        }

        // Compose birthdate from birth_day/month/year if present
        if ($request->filled('birth_day') && $request->filled('birth_month') && $request->filled('birth_year') && !$request->filled('birthdate')) {
            $d = str_pad($request->input('birth_day'), 2, '0', STR_PAD_LEFT);
            $m = str_pad($request->input('birth_month'), 2, '0', STR_PAD_LEFT);
            $y = str_pad($request->input('birth_year'), 4, '0', STR_PAD_LEFT);
            // Basic validity guard
            if (checkdate((int)$m, (int)$d, (int)$y)) {
                $request->merge(['birthdate' => "$y-$m-$d"]);
            }
        }

    $data = $request->validate([
            'firstname' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z\s\-]+$/',
                'not_regex:/[<>;=\',]/',
            ],
            'surname' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z\s\-]+$/',
                'not_regex:/[<>;=\',]/',
            ],
            'middlename' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z\s\-]*$/',
                'not_regex:/[<>;=\',]/',
            ],
            'age' => [
                'required',
                'integer',
                'between:1,120',
            ],
            'birthdate' => [
                'required',
                'date',
                'date_format:Y-m-d',
            ],
            'address' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s,\.]+$/',
                'not_regex:/[<>;=\',]/',
            ],
            'services' => 'required|string|max:150',
            'contact' => [
                'required',
                'digits:11',
                'regex:/^[0-9]{11}$/',
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                'not_regex:/[<>;=\',]/',
            ],
            'gender' => [
                'required',
                'in:Male,Female',
            ],
        'appointment_time' => 'required', // will normalize
        'appointment_date' => 'required|date',
        'branch' => 'required|string|max:120'
    ], [
            'firstname.regex' => 'First name may only contain letters, spaces, or hyphens.',
            'firstname.not_regex' => 'First name must not contain special characters <, >, ;, =, \' or ,.',
            'surname.regex' => 'Surname may only contain letters, spaces, or hyphens.',
            'surname.not_regex' => 'Surname must not contain special characters <, >, ;, =, \' or ,.',
            'middlename.regex' => 'Middle name may only contain letters, spaces, or hyphens.',
            'middlename.not_regex' => 'Middle name must not contain special characters <, >, ;, =, \' or ,.',
            'age.required' => 'Age is required.',
            'age.integer' => 'Age must be a number.',
            'age.between' => 'Age must be between 1 and 120.',
            'birthdate.required' => 'Birthdate is required.',
            'birthdate.date' => 'Birthdate must be a valid date.',
            'birthdate.date_format' => 'Birthdate must be in YYYY-MM-DD format.',
            'address.required' => 'Address is required.',
            'address.regex' => 'Address may contain letters, numbers, spaces, commas and periods only.',
            'address.not_regex' => 'Address must not contain special characters <, >, ;, =, \' or ,.',
            'contact.required' => 'Contact is required.',
            'contact.digits' => 'Contact must be exactly 11 digits.',
            'contact.regex' => 'Contact must be numbers only (11 digits).',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.not_regex' => 'Email must not contain special characters <, >, ;, =, \' or ,.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender must be Male or Female.',
            'branch.required' => 'Please select a branch.',
        ]);

        // Combine first + surname
        $fullName = trim($data['firstname'].' '.$data['surname']);

        // Normalize time (store 24h) - input might be HH:MM or with seconds
        $time = date('H:i:s', strtotime($request->input('appointment_time')));

        // Enforce 2-hour slot rules within 8:00-17:00 and prevent overlaps
        $dateStr = $request->input('appointment_date');
        $branch = $request->input('branch');
        $start = Carbon::parse($dateStr.' '.$time);
        $end = (clone $start)->addHours(2);
        $open = Carbon::parse($dateStr.' 08:00:00');
        $close = Carbon::parse($dateStr.' 17:00:00');

        // Business hours: start must be >= 08:00, end must be <= 17:00
        if ($start->lt($open) || $end->gt($close)) {
            $msg = 'Appointments must start at or after 8:00 AM and end by 5:00 PM. Each appointment lasts 2 hours.';
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg], 422);
            }
            return back()->withErrors(['appointment_time' => $msg])->withInput();
        }

        // Enforce allowed start times: 08:00, 10:00, 12:00, 14:00, 15:00
        $allowedStarts = [8, 10, 12, 14, 15];
        if (!in_array((int)$start->format('G'), $allowedStarts) || (int)$start->format('i') !== 0) {
            $msg = 'Start time must be one of: 8:00 AM, 10:00 AM, 12:00 PM, 2:00 PM, or 3:00 PM.';
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg], 422);
            }
            return back()->withErrors(['appointment_time' => $msg])->withInput();
        }

        // Resolve branch_id for FK/scoping if available; create if not existing
        $branchId = null;
        if ($branch && Schema::hasTable('tbl_branch')) {
            $branchId = DB::table('tbl_branch')->where('branch_name', $branch)->value('branch_id');
            if (!$branchId) {
                $branchId = DB::table('tbl_branch')->insertGetId([
                    'branch_name' => $branch,
                    'branch_address' => null,
                    'branch_operating_hours' => '08:00-17:00',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Prevent overlap with existing (pending or confirmed) appointments on same date (and branch if provided)
        if (!$this->isSlotAvailable($start, $end, $branch, $branchId)) {
            // Suggest next available slot on same date
            $suggestion = $this->getNextAvailableSlot($start->copy()->startOfDay(), $branch, $start, $branchId);
            $msg = 'Selected time overlaps an existing appointment. ' . ($suggestion ? ('Next available: '.$suggestion->format('g:i A').'.') : 'No more available slots for this date.');
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg, 'next_available' => $suggestion ? $suggestion->format('H:i') : null], 422);
            }
            return back()->withErrors(['appointment_time' => $msg])->withInput();
        }

        // Additional, optional fields
    $patientType = $request->input('patient_type');
    $branch = $request->input('branch');
    $gender = $data['gender'] ?? null;
    $birthdate = $data['birthdate'] ?? null;
    $contact = $request->input('contact', $request->input('phone')); // accept 'phone' alias
    $address = $data['address'] ?? null;
    $referredBy = $request->input('referred_by');
    $purpose = $request->input('purpose');
    $medicalHistory = $request->input('medical_history');

        // Resolve service_id by service name if available; create if not existing
        $serviceId = null;
        if (!empty($data['services']) && Schema::hasTable('tbl_services')) {
            $serviceId = DB::table('tbl_services')->where('service_name', $data['services'])->value('service_id');
            if (!$serviceId) {
                $serviceId = DB::table('tbl_services')->insertGetId([
                    'service_name' => $data['services'],
                    'service_price' => 0,
                    'service_status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Resolve/create patient_id from provided patient details if table exists
        $patientId = null;
        if (Schema::hasTable('tbl_patient')) {
            $firstNameIn = trim((string)$request->input('firstname', $request->input('first_name')));
            $surNameIn = trim((string)$request->input('surname'));
            // 1) Prefer name (+ birthdate) exact match to avoid wrong associations
            $qName = DB::table('tbl_patient')
                ->whereRaw('LOWER(TRIM(patient_firstname)) = ?', [mb_strtolower($firstNameIn)])
                ->whereRaw('LOWER(TRIM(patient_surname)) = ?', [mb_strtolower($surNameIn)]);
            if (!empty($birthdate)) { $qName->where('patient_birthdate', $birthdate); }
            $patientId = $qName->value('patient_id');

            // 2) If not found, attempt email match but only when names also match
            if (!$patientId && !empty($data['email'])) {
                $qEmail = DB::table('tbl_patient')->where('patient_email', $data['email'])
                    ->whereRaw('LOWER(TRIM(patient_firstname)) = ?', [mb_strtolower($firstNameIn)])
                    ->whereRaw('LOWER(TRIM(patient_surname)) = ?', [mb_strtolower($surNameIn)]);
                $patientId = $qEmail->value('patient_id');
            }

            // 3) If still not found, attempt contact match but only when names also match
            if (!$patientId && !empty($contact)) {
                $qPhone = DB::table('tbl_patient')->where('patient_contact_number', $contact)
                    ->whereRaw('LOWER(TRIM(patient_firstname)) = ?', [mb_strtolower($firstNameIn)])
                    ->whereRaw('LOWER(TRIM(patient_surname)) = ?', [mb_strtolower($surNameIn)]);
                if (!empty($birthdate)) { $qPhone->where('patient_birthdate', $birthdate); }
                $patientId = $qPhone->value('patient_id');
            }

            // 4) If no matching person, create a new patient row with provided details
            if (!$patientId) {
                $age = null;
                if ($birthdate) {
                    try {
                        $age = Carbon::parse($birthdate)->age;
                        if (!is_int($age)) { $age = (int) floor($age); }
                        if ($age < 0 || $age > 120) { $age = null; }
                    } catch (\Throwable $e) { $age = null; }
                }
                $patientId = DB::table('tbl_patient')->insertGetId([
                    'patient_firstname' => $firstNameIn,
                    'patient_surname' => $surNameIn,
                    'patient_middlename' => $request->input('middlename'),
                    'patient_birthdate' => $birthdate,
                    'patient_age' => $age,
                    'patient_gender' => $gender,
                    'patient_email' => $data['email'] ?? null,
                    'patient_contact_number' => $contact,
                    'patient_address' => $address,
                    'patient_referred_by' => $referredBy,
                    'patient_medical_history' => $medicalHistory,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Prevent a patient from booking multiple times on the same date (pending/confirmed)
        if ($patientId && Schema::hasColumn('tbl_appointment','patient_id')) {
            $already = Appointment::where('patient_id', $patientId)
                ->whereDate('appointment_date', $data['appointment_date'])
                ->when(Schema::hasColumn('tbl_appointment','status'), function($q){
                    $q->whereIn('status', ['pending','confirmed']);
                })
                ->exists();
            if ($already) {
                $msg = 'You already have an appointment on this date. Only one appointment per day is allowed.';
                if ($request->wantsJson()) {
                    return response()->json(['status' => 'error', 'message' => $msg], 422);
                }
                return back()->withErrors(['appointment_date' => $msg])->withInput();
            }
        }

        $appointment = Appointment::create([
            'appointment_time' => $time,
            'appointment_date' => $data['appointment_date'],
            'patient_type' => $patientType,
            'purpose' => $purpose,
            'status' => 'pending'
        ]);

        // Attach FKs if columns exist
        $updates = [];
        if ($patientId && Schema::hasColumn('tbl_appointment', 'patient_id')) $updates['patient_id'] = $patientId;
        if ($branchId && Schema::hasColumn('tbl_appointment', 'branch_id')) $updates['branch_id'] = $branchId;
        if ($serviceId && Schema::hasColumn('tbl_appointment', 'service_id')) $updates['service_id'] = $serviceId;
        if (!empty($updates)) { $appointment->forceFill($updates)->save(); }

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'appointment' => $appointment]);
        }
        return redirect()->back()->with('success', 'Appointment booked successfully.');
    }

    /**
     * Check if a time slot is available (no overlap) for the given start/end and optional branch.
     */
    protected function isSlotAvailable(Carbon $start, Carbon $end, ?string $branch = null, ?int $branchId = null): bool
    {
        $date = $start->toDateString();
        $query = Appointment::query()
            ->whereDate('appointment_date', $date)
            ->where(function ($q) {
                // consider pending and confirmed as blocking; ignore canceled
                if (Schema::hasColumn('tbl_appointment', 'status')) {
                    $q->whereIn('status', ['pending', 'confirmed']);
                }
            });
        if ($branch !== null && $branch !== '') {
            $query->where(function($q) use ($branch) {
                if (Schema::hasColumn('tbl_appointment', 'branch')) {
                    $q->where('branch', $branch);
                }
            });
        }
        if ($branchId !== null) {
            $query->where(function($q) use ($branchId) {
                if (Schema::hasColumn('tbl_appointment', 'branch_id')) {
                    $q->where('branch_id', $branchId);
                }
            });
        }
        $appointments = $query->get(['appointment_date','appointment_time']);

        foreach ($appointments as $a) {
            $aDateStr = date('Y-m-d', strtotime((string)$a->appointment_date));
            $aTimeStr = date('H:i:s', strtotime((string)$a->appointment_time));
            $aStart = Carbon::parse($aDateStr.' '.$aTimeStr);
            $aEnd = $aStart->copy()->addHours(2);
            // Overlap if newStart < aEnd AND newEnd > aStart
            if ($start->lt($aEnd) && $end->gt($aStart)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Compute next available 2-hour slot for the given date (startOfDay) and optional branch.
     * Returns Carbon instance at the next start time or null if none.
     */
    protected function getNextAvailableSlot(Carbon $date, ?string $branch = null, ?Carbon $from = null, ?int $branchId = null): ?Carbon
    {
        $open = $date->copy()->setTime(8, 0, 0);
        $close = $date->copy()->setTime(17, 0, 0);
        $from = $from ? $from->copy() : $open->copy();

        // Allowed candidate starts: 08:00, 10:00, 12:00, 14:00, 15:00
        $candidates = [8,10,12,14,15];
        
        foreach ($candidates as $h) {
            $cand = $date->copy()->setTime($h, 0, 0);
            if ($cand->lt($from)) continue; // only consider slots at or after baseline
            $end = $cand->copy()->addHours(2);
            if ($end->gt($close)) continue;
            if ($this->isSlotAvailable($cand, $end, $branch, $branchId)) {
                return $cand;
            }
        }
        return null;
    }

    /**
     * API: Check availability for a given date/time (and branch), return JSON with availability and suggested next slot.
     */
    public function checkSlot(Request $request)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'branch' => 'nullable|string|max:100',
        ]);
        $dateStr = $request->input('appointment_date');
        $timeStr = date('H:i:s', strtotime($request->input('appointment_time')));
        $branch = $request->input('branch');
        $branchId = null;
        if ($branch && Schema::hasTable('tbl_branch')) {
            $branchId = DB::table('tbl_branch')->where('branch_name', $branch)->value('branch_id');
        }
        $start = Carbon::parse($dateStr.' '.$timeStr);
        $end = (clone $start)->addHours(2);

        $open = Carbon::parse($dateStr.' 08:00:00');
        $close = Carbon::parse($dateStr.' 17:00:00');

        $withinHours = $start->gte($open) && $end->lte($close);
        $allowed = in_array((int)$start->format('G'), [8,10,12,14,15]) && (int)$start->format('i') === 0;
        $available = $withinHours && $allowed && $this->isSlotAvailable($start, $end, $branch, $branchId);
        $next = $available ? $start : $this->getNextAvailableSlot(Carbon::parse($dateStr.' 00:00:00'), $branch, $start, $branchId);
        return response()->json([
            'available' => $available,
            'within_hours' => $withinHours,
            'allowed_start' => $allowed,
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString(),
            'next_available' => $next ? $next->format('H:i') : null,
            'next_available_display' => $next ? $next->format('g:i A') : null,
        ]);
    }

    /**
     * API: Get only the next available slot for a date (and optional branch).
     */
    public function nextSlot(Request $request)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'branch' => 'nullable|string|max:100',
        ]);
        $date = Carbon::parse($request->input('appointment_date'))->startOfDay();
        $branch = $request->input('branch');
        $branchId = null;
        if ($branch && Schema::hasTable('tbl_branch')) {
            $branchId = DB::table('tbl_branch')->where('branch_name', $branch)->value('branch_id');
        }
        $next = $this->getNextAvailableSlot($date, $branch, null, $branchId);
        return response()->json([
            'next_available' => $next ? $next->format('H:i') : null,
            'next_available_display' => $next ? $next->format('g:i A') : null,
        ]);
    }

    /**
     * API: Get all available 2-hour slots for the given date (and optional branch).
     */
    public function availableSlots(Request $request)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'branch' => 'nullable|string|max:100',
        ]);
        $date = Carbon::parse($request->input('appointment_date'))->startOfDay();
        $branch = $request->input('branch');
        $branchId = null;
        if ($branch && Schema::hasTable('tbl_branch')) {
            $branchId = DB::table('tbl_branch')->where('branch_name', $branch)->value('branch_id');
        }
        $open = $date->copy()->setTime(8, 0, 0);
        $close = $date->copy()->setTime(17, 0, 0);
        $candidates = [8,10,12,14,15];
        $available = [];
        foreach ($candidates as $h) {
            $start = $date->copy()->setTime($h, 0);
            $end = $start->copy()->addHours(2);
            if ($start->lt($open) || $end->gt($close)) continue;
            if ($this->isSlotAvailable($start, $end, $branch, $branchId)) {
                $available[] = [
                    'value' => $start->format('H:i'),
                    'label' => $start->format('g:i A'),
                ];
            }
        }
        return response()->json(['date' => $date->toDateString(), 'slots' => $available]);
    }

    /**
     * Admin list of appointments.
     */
    public function adminIndex()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return redirect()->route('login');
        // Show confirmed and completed appointments (same as staff record)
        if (Schema::hasColumn('tbl_appointment', 'status')) {
            $appointments = Appointment::whereIn('status', ['confirmed', 'completed'])
                ->with(['patient','serviceRef','branchRef'])
                ->orderByDesc('confirmed_at')->get();
        } else {
            $appointments = Appointment::with(['patient','serviceRef','branchRef'])
                ->orderByDesc('created_at')->get();
        }
    return view('admin.admin-appointment-record', compact('appointments'));
    }

    /**
     * Staff list of new patient appointments (reuse same dataset for now).
     */
    public function staffNew()
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') return redirect()->route('login');
        // Show only pending appointments (awaiting testing)
        // Completed appointments will move to appointment record
        if (Schema::hasColumn('tbl_appointment', 'confirmed_at') && Schema::hasColumn('tbl_appointment','status')) {
            $q = Appointment::whereNotNull('confirmed_at')->where('status', 'pending');
        } elseif (Schema::hasColumn('tbl_appointment', 'confirmed_at')) {
            $q = Appointment::whereNotNull('confirmed_at');
        } elseif (Schema::hasColumn('tbl_appointment','status')) {
            // If no confirmed_at, show only pending appointments
            $q = Appointment::where('status', 'pending');
        } else {
            $q = Appointment::query();
        }
        $staffBranchId = Auth::user()->branch_id ?? null;
        $staffBranchName = Auth::user()->branch ?? null;
        // Prefer strict branch_id; fallback to legacy text; safe-by-default
        if ($staffBranchId && Schema::hasColumn('tbl_appointment','branch_id')) {
            $q->where('branch_id', $staffBranchId);
        } else if ($staffBranchName && Schema::hasColumn('tbl_appointment','branch')) {
            $q->where('branch', $staffBranchName);
        } else if (Schema::hasColumn('tbl_appointment','branch_id')) {
            // Try to resolve branch name → id if user has only legacy name
            $rid = $this->resolveBranchIdByName($staffBranchName);
            if ($rid) { $q->where('branch_id', $rid); }
            else { $q->whereRaw('1=0'); }
        } else {
            $q->whereRaw('1=0');
        }
        $appointments = $q->with(['patient','serviceRef','branchRef'])->orderByDesc('created_at')->get();
    return view('staff.staff-appointment-new-patient', compact('appointments'));
    }

    /**
     * Staff confirms an appointment
     */
    public function confirm(Request $request, Appointment $appointment)
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') return redirect()->route('login');
        // If already has a confirmed_at timestamp, don't reconfirm
        if ($appointment->confirmed_at) {
            return redirect()->route('staff.appointment.new.patient')->with('info', 'Already confirmed.');
        }
        // New flow: confirming via email should mark the appointment as "email confirmed"
        // by setting confirmed_at, but keep status as 'pending' until the patient is tested.
        $appointment->update([
            'confirmed_at' => now(),
            'canceled_at' => null
        ]);
        // TODO: dispatch email job (Mailgun/FastAPI integration)
        // New flow: redirect to Staff New Patient Appointment page after confirming
        return redirect()->route('staff.appointment.new.patient')->with('success', 'Appointment confirmed.');
    }

    /**
     * Explicitly add (or ensure) a patient record from a confirmed appointment when user clicks the green plus icon.
     */
    public function addPatientRecordFromAppointment(Request $request, Appointment $appointment)
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') return redirect()->route('login');
        // Allow confirmed and completed appointments
        if (Schema::hasColumn('tbl_appointment','status') && !in_array($appointment->status, ['confirmed', 'completed'])) {
            return redirect()->route('staff.appointment.record')->with('error', 'Appointment must be confirmed or completed before adding to patient record.');
        }
        try {
            if (!$appointment->relationLoaded('patient')) { $appointment->load('patient'); }
            $p = $appointment->patient;
            if ($p && Schema::hasTable('tbl_patient_record')) {
                try {
                    $exists = \Illuminate\Support\Facades\DB::table('tbl_patient_record')->where('patient_id', $p->patient_id)->value('patient_record_id');
                    if (!$exists) {
                        $dateRegistered = $appointment->appointment_date;
                        if ($dateRegistered instanceof \Carbon\Carbon) { $dateRegistered = $dateRegistered->format('Y-m-d 00:00:00'); }
                        elseif (is_string($dateRegistered)) { $dateRegistered = substr($dateRegistered,0,10) . ' 00:00:00'; }
                        else { $dateRegistered = date('Y-m-d 00:00:00'); }
                        \Illuminate\Support\Facades\DB::table('tbl_patient_record')->insert([
                            'patient_id' => $p->patient_id,
                            'billing_id' => null,
                            'patient_record_date_registered' => $dateRegistered,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } catch (\Throwable $e) { /* ignore */ }
            }
            if ($p) {
                $first = trim((string)($p->patient_firstname ?? ''));
                $middle = trim((string)($p->patient_middlename ?? ''));
                $last = trim((string)($p->patient_surname ?? ''));
                $gender = trim((string)($p->patient_gender ?? ''));
                $bday = $p->patient_birthdate ? (is_string($p->patient_birthdate) ? substr($p->patient_birthdate,0,10) : (method_exists($p->patient_birthdate,'format') ? $p->patient_birthdate->format('Y-m-d') : '')) : '';
                $dateReg = $appointment->appointment_date ? (is_string($appointment->appointment_date) ? substr($appointment->appointment_date,0,10) : (method_exists($appointment->appointment_date,'format') ? $appointment->appointment_date->format('Y-m-d') : date('Y-m-d'))) : date('Y-m-d');
                $ptype = '';
                if (isset($appointment->patient_type)) { $ptype = (string)$appointment->patient_type; }
                elseif (isset($p->patient_type)) { $ptype = (string)$p->patient_type; }
                $path = storage_path('app/patient_records.json');
                $rows = [];
                if (file_exists($path)) {
                    $json = @file_get_contents($path);
                    $rows = $json ? (json_decode($json, true) ?: []) : [];
                }
                $exists = false; $idx = -1;
                foreach ($rows as $i => $r) {
                    $rf = trim((string)($r['first_name'] ?? ''));
                    $rm = trim((string)($r['middle_name'] ?? ''));
                    $rl = trim((string)($r['last_name'] ?? ''));
                    $rb = trim((string)($r['birthday'] ?? ''));
                    if (strcasecmp($rf, $first) === 0 && strcasecmp($rm, $middle) === 0 && strcasecmp($rl, $last) === 0 && $rb === $bday) { $exists = true; $idx = $i; break; }
                }
                if (!$exists) {
                    $maxId = 0; foreach ($rows as $r) { $maxId = max($maxId, (int)($r['id'] ?? 0)); }
                    $newId = $maxId + 1;
                    $rows[] = [
                        'id' => $newId,
                        'first_name' => $first,
                        'middle_name' => $middle,
                        'last_name' => $last,
                        'gender' => in_array($gender, ['Male','Female','Other']) ? $gender : ($gender ?: ''),
                        'birthday' => $bday,
                        'date_registered' => $dateReg,
                        'patient_type' => $ptype,
                    ];
                    @file_put_contents($path, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
                    return redirect()->route('staff.appointment.record')->with('success', 'Patient added to record.');
                } else {
                    // Update patient_type if empty
                    if ($ptype !== '' && (!isset($rows[$idx]['patient_type']) || $rows[$idx]['patient_type'] === '')) {
                        $rows[$idx]['patient_type'] = $ptype;
                        @file_put_contents($path, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
                    }
                    return redirect()->route('staff.appointment.record')->with('info', 'Patient already exists in record.');
                }
            }
        } catch (\Throwable $e) {
            return redirect()->route('staff.appointment.record')->with('error', 'Failed to add patient record.');
        }
        return redirect()->route('staff.appointment.record')->with('error', 'No patient data available.');
    }

    /**
     * Staff cancels an appointment
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') return redirect()->route('login');
        if ($appointment->status === 'canceled') {
            return back()->with('info', 'Already canceled.');
        }
        $appointment->update([
            'status' => 'canceled',
            'canceled_at' => now()
        ]);
        return back()->with('success', 'Appointment canceled.');
    }

    /**
     * Return unseen and total appointment counts (JSON)
     */
    public function unseenCount()
    {
        // Base query: pending + confirmed only when status column exists
        $base = Appointment::query();
        if (Schema::hasColumn('tbl_appointment', 'status')) {
            $base->whereIn('status', ['pending','confirmed']);
        }
        // Scope to logged-in staff branch
        if (Auth::check() && Auth::user()->role === 'staff') {
            $staffBranchId = Auth::user()->branch_id ?? null;
            $staffBranchName = Auth::user()->branch ?? null;
            $resolvedId = $staffBranchId ?: ($this->resolveBranchIdByName($staffBranchName));
            if ($resolvedId && Schema::hasColumn('tbl_appointment','branch_id')) {
                $base->where('branch_id', $resolvedId);
            } elseif ($staffBranchName && Schema::hasColumn('tbl_appointment','branch')) {
                $base->where('branch', $staffBranchName);
            } else {
                // No branch scoping available: be safe and return nothing
                $base->whereRaw('1=0');
            }
        }
        $total = (clone $base)->count();
        $unseen = (clone $base)->whereNull('seen_at')->count();
        return response()->json([
            'total' => $total,
            'unseen' => $unseen
        ]);
    }

    /**
     * Mark all unseen appointments as seen (JSON)
     */
    public function markSeen(Request $request)
    {
        // Mark unseen as seen, scoped to staff branch when applicable
        $q = Appointment::whereNull('seen_at');
        if (Schema::hasColumn('tbl_appointment', 'status')) {
            $q->whereIn('status', ['pending','confirmed']);
        }
        if (Auth::check() && Auth::user()->role === 'staff') {
            $staffBranchId = Auth::user()->branch_id ?? null;
            $staffBranchName = Auth::user()->branch ?? null;
            $resolvedId = $staffBranchId ?: ($this->resolveBranchIdByName($staffBranchName));
            if ($resolvedId && Schema::hasColumn('tbl_appointment','branch_id')) {
                $q->where('branch_id', $resolvedId);
            } elseif ($staffBranchName && Schema::hasColumn('tbl_appointment','branch')) {
                $q->where('branch', $staffBranchName);
            } else {
                $q->whereRaw('1=0');
            }
        }
        $affected = $q->update(['seen_at' => now()]);
        return response()->json(['marked' => $affected]);
    }

    /**
     * List recent appointments (unseen first) for notification dropdown
     */
    public function listRecent(Request $request)
    {
        $limit = (int) $request->query('limit', 15);
        if ($limit < 1) $limit = 1; if ($limit > 50) $limit = 50;
        // Optional status filter: pending | confirmed | all (default: all)
    $statusParam = strtolower((string) $request->query('status', 'all'));
    $statuses = ['pending','confirmed'];
    if ($statusParam === 'pending') { $statuses = ['pending']; }
    else if ($statusParam === 'confirmed') { $statuses = ['confirmed']; }
 // Important: include FK columns so belongsTo eager-loads can resolve
        $query = Appointment::select('id','appointment_date','appointment_time','seen_at','created_at', 'patient_id', 'service_id', 'branch_id')
            ->with(['patient','serviceRef','branchRef']);
        if (Schema::hasColumn('tbl_appointment', 'status')) {
            $query->addSelect('status')->whereIn('status', $statuses);
            // If caller specifically asked for pending, exclude appointments already confirmed via email
            if ($statusParam === 'pending' && Schema::hasColumn('tbl_appointment', 'confirmed_at')) {
                $query->whereNull('confirmed_at');
            }
        }
        // Scope by staff branch if logged-in staff
        if (Auth::check() && Auth::user()->role === 'staff') {
            $staffBranchId = Auth::user()->branch_id ?? null;
            $staffBranchName = Auth::user()->branch ?? null;
            $resolvedId = $staffBranchId ?: ($this->resolveBranchIdByName($staffBranchName));
            if ($resolvedId && Schema::hasColumn('tbl_appointment','branch_id')) {
                $query->where('branch_id', $resolvedId);
            } else if ($staffBranchName && Schema::hasColumn('tbl_appointment','branch')) {
                $query->where('branch', $staffBranchName);
            } else {
                $query->whereRaw('1=0');
            }
        }

        $appointments = $query
            ->orderByRaw('CASE WHEN seen_at IS NULL THEN 0 ELSE 1 END') // unseen first
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function($a){
                // Patient details
                $p = $a->relationLoaded('patient') ? $a->patient : null;
                $first = $p ? ($p->patient_firstname ?? '') : '';
                $last  = $p ? ($p->patient_surname ?? '') : '';
                $middle = $p ? ($p->patient_middlename ?? '') : '';
                $gender = $p ? ($p->patient_gender ?? null) : null;
                $email = $p ? ($p->patient_email ?? null) : null;
                $contact = $p ? ($p->patient_contact_number ?? null) : null;
                $address = $p ? ($p->patient_address ?? null) : null;
                $referredBy = $p ? ($p->patient_referred_by ?? null) : null;

                // Service and branch names
                $serviceName = ($a->relationLoaded('serviceRef') && $a->serviceRef) ? ($a->serviceRef->service_name ?? '') : '';
                $branchName = ($a->relationLoaded('branchRef') && $a->branchRef) ? ($a->branchRef->branch_name ?? '') : null;

                // Normalize dates
                $dateYmd = null;
                try {
                    $dateYmd = $a->appointment_date ? \Carbon\Carbon::parse($a->appointment_date)->format('Y-m-d') : null;
                } catch (\Throwable $e) { $dateYmd = null; }

                $birthYmd = null;
                try {
                    $birthYmd = $p && $p->patient_birthdate ? \Carbon\Carbon::parse($p->patient_birthdate)->format('Y-m-d') : null;
                } catch (\Throwable $e) { $birthYmd = null; }

                return [
                    'id' => $a->id,
                    // Display
                    'name' => trim(trim($first).' '.trim($last)),
                    'services' => $serviceName,
                    'date' => $dateYmd,
                    'time' => $a->appointment_time ? date('g:i A', strtotime((string)$a->appointment_time)) : null,
                    'seen' => $a->seen_at !== null,
                    'created_at' => $a->created_at->diffForHumans(),
                    // Detailed fields for dashboard modal
                    'first_name' => $first,
                    'surname' => $last,
                    'middlename' => $middle,
                    'gender' => $gender,
                    'email' => $email,
                    'contact' => $contact,
                    'address' => $address,
                    'referred_by' => $referredBy,
                    'branch' => $branchName,
                    'birthdate' => $birthYmd,
                    'appointment_date' => $dateYmd,
                    'appointment_time' => $a->appointment_time ? date('H:i', strtotime((string)$a->appointment_time)) : null,
                ];
            });
        return response()->json(['data' => $appointments]);
    }

    /**
     * Staff schedule view: show confirmed appointments to block time slots
     */
    public function staffSchedule(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') return redirect()->route('login');
        $dateParam = $request->query('date');
        $date = $dateParam ? Carbon::parse($dateParam)->toDateString() : Carbon::today()->toDateString();

        $base = Schema::hasColumn('tbl_appointment', 'status')
            ? Appointment::where('status', 'confirmed')
            : Appointment::query();

        // Scope to staff branch
        $staffBranchId = Auth::user()->branch_id ?? null;
        $staffBranchName = Auth::user()->branch ?? null;

        $appointments = $base
            ->whereDate('appointment_date', $date)
            ->when($staffBranchId && Schema::hasColumn('tbl_appointment','branch_id'), function($q) use ($staffBranchId){ $q->where('branch_id', $staffBranchId); })
            ->when((!$staffBranchId) && $staffBranchName && Schema::hasColumn('tbl_appointment','branch'), function($q) use ($staffBranchName){ $q->where('branch', $staffBranchName); })
            ->when((!$staffBranchId) && Schema::hasColumn('tbl_appointment','branch_id') && $this->resolveBranchIdByName($staffBranchName), function($q) use ($staffBranchName){
                $rid = $this->resolveBranchIdByName($staffBranchName);
                if ($rid) { $q->where('branch_id', $rid); }
            })
            ->when((!$staffBranchId) && (!$staffBranchName || !Schema::hasColumn('tbl_appointment','branch')), function($q){ $q->whereRaw('1=0'); })
            ->with(['patient','serviceRef','branchRef'])
            ->orderBy('appointment_time')
            ->get();

        $displayDate = Carbon::parse($date)->format('D, F d, Y');
    return view('staff.staff-appointment-schedule', compact('appointments','displayDate','date'));
    }

    /**
     * Staff patient record: list confirmed appointments as registered patients.
     */
    public function staffPatients()
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') return redirect()->route('login');
        $q = Schema::hasColumn('tbl_appointment', 'status')
            ? Appointment::whereIn('status', ['confirmed', 'completed'])
            : Appointment::query();
        // Scope to staff branch
        $staffBranchId = Auth::user()->branch_id ?? null;
        $staffBranchName = Auth::user()->branch ?? null;
    if ($staffBranchId && Schema::hasColumn('tbl_appointment','branch_id')) { $q->where('branch_id', $staffBranchId); }
    else if ($staffBranchName && Schema::hasColumn('tbl_appointment','branch')) { $q->where('branch', $staffBranchName); }
    else if (Schema::hasColumn('tbl_appointment','branch_id') && ($rid = $this->resolveBranchIdByName($staffBranchName))) { $q->where('branch_id', $rid); }
    else { $q->whereRaw('1=0'); }
        $appointments = $q->with(['patient','serviceRef','branchRef'])
            ->when(Schema::hasColumn('tbl_appointment','confirmed_at'), function($q){ $q->orderByDesc('confirmed_at'); }, function($q){ $q->orderByDesc('created_at'); })
            ->get();
        $fileIds = $this->mapAppointmentsToFileRecordIds($appointments);
    return view('staff.staff-patient-record', compact('appointments','fileIds'));
    }

    /**
     * Admin patient record: list confirmed appointments as registered patients.
     */
    public function adminPatients()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return redirect()->route('login');
        $q = Schema::hasColumn('tbl_appointment', 'status')
            ? Appointment::whereIn('status', ['confirmed', 'completed'])
            : Appointment::query();
        $appointments = $q->with(['patient','serviceRef','branchRef'])
            ->when(Schema::hasColumn('tbl_appointment','confirmed_at'), function($q){ $q->orderByDesc('confirmed_at'); }, function($q){ $q->orderByDesc('created_at'); })
            ->get();
        $fileIds = $this->mapAppointmentsToFileRecordIds($appointments);
    return view('admin.admin-patient-record', compact('appointments','fileIds'));
    }

    /**
     * Staff appointment record: mirror admin confirmed list for staff view.
     */
    public function staffRecord()
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') return redirect()->route('login');
        $q = Schema::hasColumn('tbl_appointment', 'status')
            ? Appointment::whereIn('status', ['confirmed', 'completed'])
            : Appointment::query();
        $staffBranchId = Auth::user()->branch_id ?? null;
        $staffBranchName = Auth::user()->branch ?? null;
    if ($staffBranchId && Schema::hasColumn('tbl_appointment','branch_id')) { $q->where('branch_id', $staffBranchId); }
    else if ($staffBranchName && Schema::hasColumn('tbl_appointment','branch')) { $q->where('branch', $staffBranchName); }
    else if (Schema::hasColumn('tbl_appointment','branch_id') && ($rid = $this->resolveBranchIdByName($staffBranchName))) { $q->where('branch_id', $rid); }
    else { $q->whereRaw('1=0'); }
        $appointments = $q->with(['patient','serviceRef','branchRef','tests'])
            ->when(Schema::hasColumn('tbl_appointment','confirmed_at'), function($q){ $q->orderByDesc('confirmed_at'); }, function($q){ $q->orderByDesc('created_at'); })
            ->paginate(10);
        // Build svcRecords per appointment and billing summaries for modal rendering
        $svcRecordsPerAppt = [];
        $billingPerAppt = [];
        $reverseMap = [
            'Otoacoustic Emission (OAE)' => 'oae',
            'Pure Tone Audiometry' => 'pta',
            'Tympanometry' => 'tym',
            'Speech Audiometry' => 'speech',
            'Auditory Brainstem Response (ABR)' => 'abr',
            'Auditory Brain Response' => 'abr',
            'Auditory Steady State Response (ASSR)' => 'assr',
            'Aided Testing' => 'aided',
            'Play Audiometry' => 'play',
        ];
        $priceMap = [
            'pta' => ['R' => 1000, 'SC' => 800, 'PWD' => 800],
            'speech' => ['R' => 625, 'SC' => 500, 'PWD' => 500],
            'tym' => ['R' => 635, 'SC' => 500, 'PWD' => 500],
            'abr' => ['R' => 7500, 'SC' => 6000, 'PWD' => 6000],
            'assr' => ['R' => 7500, 'SC' => 6000, 'PWD' => 6000],
            'oae' => ['R' => 500, 'SC' => 500, 'PWD' => 500],
            'aided' => ['R' => 1000, 'SC' => 800, 'PWD' => 800],
            // Play Audiometry: no senior discount; PWD gets 20%
            'play' => ['R' => 3700, 'SC' => 3700, 'PWD' => 2960],
            // Hearing Aid Models with 20% discount for SC and PWD
            'hearing_tmaxx600_chargable' => ['R' => 105000, 'SC' => 84000, 'PWD' => 84000],
            'hearing_tmaxx600_battery' => ['R' => 65000, 'SC' => 52000, 'PWD' => 52000],
            'hearing_stridep500_chargable' => ['R' => 120000, 'SC' => 96000, 'PWD' => 96000],
            'hearing_stridep500_battery' => ['R' => 80000, 'SC' => 64000, 'PWD' => 64000],
        ];
        $labelMap = [
            'pta' => 'Pure Tone Audiometry',
            'speech' => 'Speech Audiometry',
            'tym' => 'Tympanometry',
            'abr' => 'Auditory Brainstem Response (ABR)',
            'assr' => 'Auditory Steady State Response (ASSR)',
            'oae' => 'Otoacoustic Emission (OAE)',
            'aided' => 'Aided Testing',
            'play' => 'Play Audiometry',
            // Hearing Aid Models
            'hearing_tmaxx600_chargable' => 'TMAXX600 Chargable',
            'hearing_tmaxx600_battery' => 'TMAXX600 Battery',
            'hearing_stridep500_chargable' => 'StrideP500 Chargable',
            'hearing_stridep500_battery' => 'StrideP500 Battery',
        ];
        foreach ($appointments as $a) {
            $svcMap = [];
            $tests = $a->relationLoaded('tests') ? $a->tests : collect();
            if (!$tests || !$tests->count()) {
                if (Schema::hasTable('tbl_test')) {
                    $tests = \App\Models\Test::where(function($q) use ($a){
                        $q->where('patient_id', (int) ($a->patient_id ?? 0));
                        if ($a->id) { $q->orWhere('appointment_id', $a->id); }
                    })->orderByDesc('test_date')->get();
                } else {
                    $tests = collect();
                }
            }
            foreach ($tests as $t) {
                $payload = [];
                if (!empty($t->test_payload)) { $dec = json_decode($t->test_payload, true); if (is_array($dec)) $payload = $dec; }
                $svcKey = $reverseMap[$t->test_type] ?? null;
                if (!$svcKey) continue;
                $base = array_merge($payload, [
                    'id' => spl_object_id($t),
                    'patient_id' => (int) ($a->patient_id ?? 0),
                    'service' => $svcKey,
                    'date_taken' => $t->test_date,
                    'created_at' => ($t->test_date ? ($t->test_date.' 00:00:00') : null),
                ]);
                $svcMap[$svcKey][] = $base;
            }
            
            // Add hearing aid data from session
            $haAll = request()->session()->get('ha_session_records', []);
            $patientId = (int)($a->patient_id ?? 0);
            if (is_array($haAll) && isset($haAll[$patientId])) {
                $hearingAids = $haAll[$patientId];
                if (is_array($hearingAids) && count($hearingAids) > 0) {
                    $svcMap['hearing'] = $hearingAids;
                    // Debug: log to see if data is being loaded
                    \Log::info('Hearing aid data loaded for patient ' . $patientId . ': ' . json_encode($hearingAids));
                }
            }
            
            $svcRecordsPerAppt[$a->id] = $svcMap;
            // Billing summary from svcMap
            $ptFull = $a->patient_type ?? 'Regular';
            $t = strtolower(trim((string) $ptFull));
            $ptCode = (str_contains($t,'sen') ? 'SC' : (($t==='pwd'||str_contains($t,'dis')) ? 'PWD' : 'R'));
            $items = [];
            $regularTotal = 0.0; $finalTotal = 0.0; $discountTotal = 0.0;
            foreach ($svcMap as $svcKey => $rows) {
                if ($svcKey === 'hearing') {
                    // Handle hearing aid pricing
                    foreach ($rows as $ha) {
                        $model = strtolower(str_replace(' ', '_', $ha['model'] ?? ''));
                        $haServiceKey = 'hearing_' . $model;
                        
                        if (isset($priceMap[$haServiceKey])) {
                            $regular = $priceMap[$haServiceKey]['R'];
                            $final = $priceMap[$haServiceKey][$ptCode] ?? $regular;
                            $discount = 0.0;
                            if ($ptCode !== 'R') { $discount = $regular - $final; }
                            $items[] = [
                                'label' => $labelMap[$haServiceKey] ?? $ha['model'],
                                'regular' => $regular,
                                'final' => $final,
                                'discount' => $discount,
                            ];
                            $regularTotal += $regular; $finalTotal += $final; $discountTotal += $discount;
                        }
                    }
                } else {
                    // Handle regular services
                    if (!isset($priceMap[$svcKey])) continue;
                    foreach ($rows as $_) {
                        $regular = $priceMap[$svcKey]['R'];
                        $final = $priceMap[$svcKey][$ptCode] ?? $regular;
                        $discount = 0.0;
                        if ($svcKey !== 'oae' && $ptCode !== 'R') { $discount = $regular - $final; }
                        $items[] = [
                            'label' => $labelMap[$svcKey] ?? strtoupper($svcKey),
                            'regular' => $regular,
                            'final' => $final,
                            'discount' => $discount,
                        ];
                        $regularTotal += $regular; $finalTotal += $final; $discountTotal += $discount;
                    }
                }
            }
            $billingPerAppt[$a->id] = [
                'patient_type' => $ptFull,
                'items' => $items,
                'regularTotal' => $regularTotal,
                'discountTotal' => $discountTotal,
                'finalTotal' => $finalTotal,
            ];
        }
    return view('staff.staff-appointment-record', compact('appointments','svcRecordsPerAppt','billingPerAppt'));
    }

    /**
     * API: Today's schedule for staff dashboard (confirmed appointments only)
     */
    public function todaySchedule(Request $request)
    {
        $dateParam = $request->query('date');
        $today = $dateParam ? Carbon::parse($dateParam) : Carbon::today();
        $statusParam = strtolower((string) $request->query('status', 'confirmed'));
        $filterStatuses = $statusParam === 'pending' ? ['pending'] : ($statusParam === 'all' ? ['pending','confirmed'] : ['confirmed']);
        $base = Schema::hasColumn('tbl_appointment', 'status')
            ? Appointment::whereDate('appointment_date', $today)->whereIn('status', $filterStatuses)
            : Appointment::whereDate('appointment_date', $today);

        // Scope by staff branch if staff
        if (Auth::check() && Auth::user()->role === 'staff') {
            $staffBranchId = Auth::user()->branch_id ?? null;
            $staffBranchName = Auth::user()->branch ?? null;
            $resolvedId = $staffBranchId ?: ($this->resolveBranchIdByName($staffBranchName));
            if ($resolvedId && Schema::hasColumn('tbl_appointment','branch_id')) {
                $base->where('branch_id', $resolvedId);
            } else if ($staffBranchName && Schema::hasColumn('tbl_appointment','branch')) {
                $base->where('branch', $staffBranchName);
            } else {
                $base->whereRaw('1=0');
            }
        }

        $appointments = $base
            ->with(['patient','serviceRef'])
            ->orderBy('appointment_time')
            ->get()
            ->map(function($a){
                $name = '';
                if ($a->relationLoaded('patient') && $a->patient) {
                    $first = $a->patient->patient_firstname ?? '';
                    $last  = $a->patient->patient_surname ?? '';
                    $name = trim(trim($first).' '.trim($last));
                }
                $serviceName = ($a->relationLoaded('serviceRef') && $a->serviceRef) ? ($a->serviceRef->service_name ?? '') : '';
                return [
                    'id' => $a->id,
                    'time' => $a->appointment_time ? date('g:i A', strtotime((string)$a->appointment_time)) : null,
                    'time_raw' => $a->appointment_time ? date('H:i', strtotime((string)$a->appointment_time)) : null,
                    'name' => $name,
                    'service' => $serviceName,
                ];
            });
        return response()->json(['date' => $today->toDateString(), 'appointments' => $appointments]);
    }

    /**
     * Admin delete appointment record
     */
    public function adminDelete(Appointment $appointment)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return redirect()->route('login');
        $appointment->delete();
        return back()->with('success', 'Appointment deleted.');
    }

    /**
     * Staff delete appointment record
     */
    public function staffDelete(Appointment $appointment)
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') return redirect()->route('login');
        $appointment->delete();
        return back()->with('success', 'Appointment deleted.');
    }

    /**
     * Admin appointment report - fetch all appointments with related data
     */
    public function adminReport()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return redirect()->route('login');
        
        // Fetch all appointments with their related data
        $appointments = Appointment::with(['patient', 'serviceRef', 'branchRef'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.admin-report-appointment', compact('appointments'));
    }

    /**
     * Server-Sent Events stream for real-time notifications
     */
    public function streamNotifications()
    {
        // Set headers for SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Cache-Control');

        // Send initial connection event
        echo "data: " . json_encode(['type' => 'connected', 'timestamp' => time()]) . "\n\n";
        flush();

        $lastCount = 0;
        $lastCheck = time();

        while (true) {
            // Check for new appointments every 1 second
            $unseenCount = Appointment::where('status', 'pending')
                ->where('seen', false)
                ->count();

            // Send update if count changed
            if ($unseenCount !== $lastCount) {
                $lastCount = $unseenCount;
                echo "data: " . json_encode([
                    'type' => 'notification_update',
                    'count' => $unseenCount,
                    'timestamp' => time()
                ]) . "\n\n";
                flush();
            }

            // Send heartbeat every 30 seconds to keep connection alive
            if (time() - $lastCheck > 30) {
                echo "data: " . json_encode(['type' => 'heartbeat', 'timestamp' => time()]) . "\n\n";
                flush();
                $lastCheck = time();
            }

            // Sleep for 1 second before next check
            sleep(1);
        }
    }
}
