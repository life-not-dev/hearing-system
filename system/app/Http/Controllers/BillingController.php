<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    protected array $priceMap = [
        'pta' => ['R' => 1000, 'SC' => 800, 'PWD' => 800],
        'speech' => ['R' => 625, 'SC' => 500, 'PWD' => 500],
        'tym' => ['R' => 635, 'SC' => 500, 'PWD' => 500],
        'abr' => ['R' => 7500, 'SC' => 6000, 'PWD' => 6000],
        'assr' => ['R' => 7500, 'SC' => 6000, 'PWD' => 6000],
        'oae' => ['R' => 500, 'SC' => 500, 'PWD' => 500], // no displayed discount for OAE
        'aided' => ['R' => 1000, 'SC' => 800, 'PWD' => 800],
        // Play Audiometry: no senior discount; PWD gets 20%
        'play' => ['R' => 3700, 'SC' => 3700, 'PWD' => 2960],
        // Hearing Aid Models with 20% discount for SC and PWD
        'hearing_tmaxx600_chargable' => ['R' => 105000, 'SC' => 84000, 'PWD' => 84000],
        'hearing_tmaxx600_battery' => ['R' => 65000, 'SC' => 52000, 'PWD' => 52000],
        'hearing_stridep500_chargable' => ['R' => 120000, 'SC' => 96000, 'PWD' => 96000],
        'hearing_stridep500_battery' => ['R' => 80000, 'SC' => 64000, 'PWD' => 64000],
    ];

    protected array $serviceLabels = [
        'pta' => 'Pure Tone Audiometry',
        'speech' => 'Speech Audiometry',
        'tym' => 'Tympanometry',
        'abr' => 'Auditory Brain Response',
        'assr' => 'Auditory Steady State Response',
        'oae' => 'Oto Acoustic with Emession',
        'aided' => 'Aided Testing',
        'play' => 'Play Audiometry',
        // Hearing Aid Models
        'hearing_tmaxx600_chargable' => 'TMAXX600 Chargable',
        'hearing_tmaxx600_battery' => 'TMAXX600 Battery',
        'hearing_stridep500_chargable' => 'StrideP500 Chargable',
        'hearing_stridep500_battery' => 'StrideP500 Battery',
    ];

    public function index(Request $request)
    {
        $patients = $this->loadPatientRecords();
        $svcAll = $request->session()->get('svc_session_records', []); // patient_id => [service => [records]]
        $haAll = $request->session()->get('ha_session_records', []); // patient_id => hearing aids

        $rows = [];
        $grouped = collect(); // Initialize as empty collection
        // Preload latest appointment patient_type per patient_id as fallback
        $latestApptType = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_appointment') && \Illuminate\Support\Facades\Schema::hasColumn('tbl_appointment','patient_id')) {
            try {
                $apptRows = \Illuminate\Support\Facades\DB::table('tbl_appointment')
                    ->select('patient_id','patient_type','appointment_date')
                    ->whereNotNull('patient_id')
                    ->orderByDesc('appointment_date')
                    ->get();
                foreach ($apptRows as $ar) {
                    $pid = (int)($ar->patient_id ?? 0);
                    if ($pid && !isset($latestApptType[$pid])) {
                        $latestApptType[$pid] = (string)($ar->patient_type ?? '');
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }
        // Preload DB patients for name fallback when file-based patient record is missing
        $dbPatients = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_patient')) {
            try {
                $prs = \Illuminate\Support\Facades\DB::table('tbl_patient')
                    ->select('patient_id','patient_firstname','patient_middlename','patient_surname')
                    ->get();
                foreach ($prs as $pr) {
                    $pid = (int)($pr->patient_id ?? 0);
                    if ($pid) {
                        $dbPatients[$pid] = [
                            'first_name' => (string)($pr->patient_firstname ?? ''),
                            'middle_name' => (string)($pr->patient_middlename ?? ''),
                            'last_name' => (string)($pr->patient_surname ?? ''),
                        ];
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }
        $seq = 1;
        // Get all patient IDs from both service records and hearing aid records
        $allPatientIds = array_unique(array_merge(array_keys($svcAll), array_keys($haAll)));
        
        foreach ($allPatientIds as $patientId) {
            $patientId = (int)$patientId;
            $svcMap = $svcAll[$patientId] ?? [];
            $p = $patients[$patientId] ?? [];
            if (!$p && isset($dbPatients[$patientId])) { $p = $dbPatients[$patientId]; }
            $patientTypeFull = $p['patient_type'] ?? ($latestApptType[$patientId] ?? 'Regular');
            $ptCode = $this->mapPatientTypeCode($patientTypeFull);

            $serviceNames = [];
            $regularTotal = 0.0;
            $finalTotal = 0.0;
            $discountTotal = 0.0;
            $latestDate = null;

            foreach ($svcMap as $svcKey => $records) {
                if (!isset($this->priceMap[$svcKey])) continue;
                $label = $this->serviceLabels[$svcKey] ?? strtoupper($svcKey);
                foreach ($records as $r) {
                    $serviceNames[] = $label;
                    $regular = $this->priceMap[$svcKey]['R'];
                    $final = $this->priceMap[$svcKey][$ptCode] ?? $regular;
                    $discount = 0.0;
                    if ($svcKey !== 'oae' && $ptCode !== 'R') {
                        $discount = $regular - $final;
                    }
                    $regularTotal += $regular;
                    $finalTotal += $final;
                    $discountTotal += $discount;
                    $d = $r['date_taken'] ?? ($r['created_at'] ?? null);
                    if ($d && (!$latestDate || strcmp($d, $latestDate) > 0)) {
                        $latestDate = $d;
                    }
                }
            }

            // Process hearing aid data even if no other services
            // if (empty($serviceNames)) continue;

            $haSummary = '';
            if (!empty($haAll[$patientId])) {
                $deviceNames = [];
                foreach ($haAll[$patientId] as $ha) {
                    $deviceNames[] = ($ha['model'] ?? 'Unknown Device');
                    
                    // Add hearing aid pricing to billing calculation - DUAL PRICING
                    // 1. Hearing Aid Fitting Service
                    $serviceKey = 'hearing';
                    if (isset($this->priceMap[$serviceKey])) {
                        $serviceNames[] = $this->serviceLabels[$serviceKey] ?? 'Hearing Aid Fitting';
                        $regular = $this->priceMap[$serviceKey]['R'];
                        $final = $this->priceMap[$serviceKey][$ptCode] ?? $regular;
                        $discount = 0.0;
                        if ($ptCode !== 'R') {
                            $discount = $regular - $final;
                        }
                        $regularTotal += $regular;
                        $finalTotal += $final;
                        $discountTotal += $discount;
                    }
                    
                    // 2. Hearing Aid Device (based on model)
                    $model = strtolower(str_replace(' ', '_', $ha['model'] ?? ''));
                    $deviceServiceKey = 'hearing_' . $model;
                    if (isset($this->priceMap[$deviceServiceKey])) {
                        // Don't add device to services - it will be shown in hearing aid column
                        $regular = $this->priceMap[$deviceServiceKey]['R'];
                        $final = $this->priceMap[$deviceServiceKey][$ptCode] ?? $regular;
                        $discount = 0.0;
                        if ($ptCode !== 'R') {
                            $discount = $regular - $final;
                        }
                        $regularTotal += $regular;
                        $finalTotal += $final;
                        $discountTotal += $discount;
                    }
                }
                $haSummary = implode(', ', array_unique($deviceNames));
            }

            // Only create billing record if we have services OR hearing aids
            if (empty($serviceNames) && empty($haSummary)) {
                continue;
            }

            $rows[] = [
                'seq' => $seq++,
                'patient_id' => $patientId,
                'patient_name' => $this->composeName($p) ?: 'Unknown',
                'patient_type' => $patientTypeFull,
                'services' => implode(', ', $serviceNames),
                'hearing_aid' => $haSummary,
                'date' => $latestDate ? date('Y-m-d', strtotime($latestDate)) : date('Y-m-d'),
                'bill' => $regularTotal,
                'discount' => $discountTotal,
                'total' => $finalTotal,
            ];
        }

        // Always check DB billing as well to ensure we don't miss any records
        // This ensures that both session data and DB data are combined
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_billing')) {
            $dbRows = [];
            $billings = \App\Models\Billing::with('test')->get();
            if ($billings->count()) {
                $grouped = $billings->groupBy('patient_id');
                $seq = 1;
                foreach ($grouped as $patientId => $items) {
                    $patientId = (int)$patientId;
                    $p = $patients[$patientId] ?? [];
                    if (!$p && isset($dbPatients[$patientId])) { $p = $dbPatients[$patientId]; }
                    $patientTypeFull = $p['patient_type'] ?? ($items->first()->billing_patient_type ?? ($latestApptType[$patientId] ?? 'Regular'));
                    $serviceNames = [];
                    $hearingAidDetails = [];
                    $regularTotal = 0.0; $finalTotal = 0.0; $discountTotal = 0.0; $latestDate = null;
                    foreach ($items as $b) {
                        $regularTotal += (float)$b->billing_original_bill;
                        $finalTotal += (float)$b->billing_total_bill;
                        $discountTotal += (float)$b->billing_discount_bill;
                        $d = $b->billing_date; if ($d && (!$latestDate || strcmp($d, $latestDate) > 0)) { $latestDate = $d; }
                        if ($b->test && $b->test->test_type) { 
                            // Show "Hearing Aid Fitting" for the service, not the model name
                            if ($b->test->test_type === 'Hearing Aid Fitting') {
                                $serviceNames[] = 'Hearing Aid Fitting';
                            } elseif (strpos($b->test->test_type, 'Hearing Aid Device -') === 0) {
                                // Extract device model for hearing aid column
                                $deviceModel = str_replace('Hearing Aid Device - ', '', $b->test->test_type);
                                $hearingAidDetails[] = $deviceModel;
                            } else {
                                $serviceNames[] = $b->test->test_type;
                            }
                        }
                    }
                    $dbRows[] = [
                        'seq' => $seq++,
                        'patient_id' => $patientId,
                        'patient_name' => $this->composeName($p) ?: 'Unknown',
                        'patient_type' => $patientTypeFull,
                        'services' => implode(', ', array_unique($serviceNames)),
                        'hearing_aid' => implode(', ', array_unique($hearingAidDetails)),
                        'date' => $latestDate ? date('Y-m-d', strtotime($latestDate)) : date('Y-m-d'),
                        'bill' => $regularTotal,
                        'discount' => $discountTotal,
                        'total' => $finalTotal,
                    ];
                }
            }
            // Merge DB billing data with session data
            // Use DB data for patients not in session, or combine if both exist
            $existingPatientIds = array_column($rows, 'patient_id');
            if (!isset($grouped)) {
                $grouped = collect(); // Initialize as empty collection if not set
            }
            foreach ($grouped as $patientId => $items) {
                $patientId = (int)$patientId;
                
                // Skip if we already processed this patient from session data
                if (in_array($patientId, $existingPatientIds)) {
                    continue;
                }
                
                $p = $patients[$patientId] ?? [];
                if (!$p && isset($dbPatients[$patientId])) { $p = $dbPatients[$patientId]; }
                $patientTypeFull = $p['patient_type'] ?? ($items->first()->billing_patient_type ?? ($latestApptType[$patientId] ?? 'Regular'));
                $serviceNames = [];
                $hearingAidDetails = [];
                $regularTotal = 0.0; $finalTotal = 0.0; $discountTotal = 0.0; $latestDate = null;
                foreach ($items as $b) {
                    $regularTotal += (float)$b->billing_original_bill;
                    $finalTotal += (float)$b->billing_total_bill;
                    $discountTotal += (float)$b->billing_discount_bill;
                    $d = $b->billing_date; if ($d && (!$latestDate || strcmp($d, $latestDate) > 0)) { $latestDate = $d; }
                    if ($b->test && $b->test->test_type) { 
                        if ($b->test->test_type === 'Hearing Aid Fitting') {
                            $serviceNames[] = 'Hearing Aid Fitting';
                        } elseif (strpos($b->test->test_type, 'Hearing Aid Device -') === 0) {
                            $deviceModel = str_replace('Hearing Aid Device - ', '', $b->test->test_type);
                            $hearingAidDetails[] = $deviceModel;
                        } else {
                            $serviceNames[] = $b->test->test_type;
                        }
                    }
                }
                $rows[] = [
                    'seq' => count($rows) + 1,
                    'patient_id' => $patientId,
                    'patient_name' => $this->composeName($p) ?: 'Unknown',
                    'patient_type' => $patientTypeFull,
                    'services' => implode(', ', array_unique($serviceNames)),
                    'hearing_aid' => implode(', ', array_unique($hearingAidDetails)),
                    'date' => $latestDate ? date('Y-m-d', strtotime($latestDate)) : date('Y-m-d'),
                    'bill' => $regularTotal,
                    'discount' => $discountTotal,
                    'total' => $finalTotal,
                ];
            }
        }

        return view('staff.staff-billing', ['rows' => $rows]);
    }

    public function adminIndex(Request $request)
    {
        // Use the same data fetching logic as staff billing
        $patients = $this->loadPatientRecords();
        $svcAll = $request->session()->get('svc_session_records', []); // patient_id => [service => [records]]
        $haAll = $request->session()->get('ha_session_records', []); // patient_id => hearing aids

        $rows = [];
        // Preload latest appointment patient_type per patient_id as fallback
        $latestApptType = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_appointment') && \Illuminate\Support\Facades\Schema::hasColumn('tbl_appointment','patient_id')) {
            try {
                $apptRows = \Illuminate\Support\Facades\DB::table('tbl_appointment')
                    ->select('patient_id','patient_type','appointment_date')
                    ->whereNotNull('patient_id')
                    ->orderByDesc('appointment_date')
                    ->get();
                foreach ($apptRows as $ar) {
                    $pid = (int)($ar->patient_id ?? 0);
                    if ($pid && !isset($latestApptType[$pid])) {
                        $latestApptType[$pid] = (string)($ar->patient_type ?? '');
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }
        // Preload DB patients for name fallback when file-based patient record is missing
        $dbPatients = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_patient')) {
            try {
                $prs = \Illuminate\Support\Facades\DB::table('tbl_patient')
                    ->select('patient_id','patient_firstname','patient_middlename','patient_surname')
                    ->get();
                foreach ($prs as $pr) {
                    $pid = (int)($pr->patient_id ?? 0);
                    if ($pid) {
                        $dbPatients[$pid] = [
                            'first_name' => (string)($pr->patient_firstname ?? ''),
                            'middle_name' => (string)($pr->patient_middlename ?? ''),
                            'last_name' => (string)($pr->patient_surname ?? ''),
                        ];
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }
        $seq = 1;
        // Get all patient IDs from both service records and hearing aid records
        $allPatientIds = array_unique(array_merge(array_keys($svcAll), array_keys($haAll)));
        
        foreach ($allPatientIds as $patientId) {
            $patientId = (int)$patientId;
            $svcMap = $svcAll[$patientId] ?? [];
            $p = $patients[$patientId] ?? [];
            if (!$p && isset($dbPatients[$patientId])) { $p = $dbPatients[$patientId]; }
            $patientTypeFull = $p['patient_type'] ?? ($latestApptType[$patientId] ?? 'Regular');
            $ptCode = $this->mapPatientTypeCode($patientTypeFull);

            $serviceNames = [];
            $regularTotal = 0.0;
            $finalTotal = 0.0;
            $discountTotal = 0.0;
            $latestDate = null;

            foreach ($svcMap as $svcKey => $records) {
                if (!isset($this->priceMap[$svcKey])) continue;
                $label = $this->serviceLabels[$svcKey] ?? strtoupper($svcKey);
                foreach ($records as $r) {
                    $serviceNames[] = $label;
                    $regular = $this->priceMap[$svcKey]['R'];
                    $final = $this->priceMap[$svcKey][$ptCode] ?? $regular;
                    $discount = 0.0;
                    if ($svcKey !== 'oae' && $ptCode !== 'R') {
                        $discount = $regular - $final;
                    }
                    $regularTotal += $regular;
                    $finalTotal += $final;
                    $discountTotal += $discount;
                    $d = $r['date_taken'] ?? ($r['created_at'] ?? null);
                    if ($d && (!$latestDate || strcmp($d, $latestDate) > 0)) {
                        $latestDate = $d;
                    }
                }
            }

            // Process hearing aid data even if no other services
            // if (empty($serviceNames)) continue;

            $haSummary = '';
            if (!empty($haAll[$patientId])) {
                $deviceNames = [];
                foreach ($haAll[$patientId] as $ha) {
                    $deviceNames[] = ($ha['model'] ?? 'Unknown Device');
                    
                    // Add hearing aid pricing to billing calculation - DUAL PRICING
                    // 1. Hearing Aid Fitting Service
                    $serviceKey = 'hearing';
                    if (isset($this->priceMap[$serviceKey])) {
                        $serviceNames[] = $this->serviceLabels[$serviceKey] ?? 'Hearing Aid Fitting';
                        $regular = $this->priceMap[$serviceKey]['R'];
                        $final = $this->priceMap[$serviceKey][$ptCode] ?? $regular;
                        $discount = 0.0;
                        if ($ptCode !== 'R') {
                            $discount = $regular - $final;
                        }
                        $regularTotal += $regular;
                        $finalTotal += $final;
                        $discountTotal += $discount;
                    }
                    
                    // 2. Hearing Aid Device (based on model)
                    $model = strtolower(str_replace(' ', '_', $ha['model'] ?? ''));
                    $deviceServiceKey = 'hearing_' . $model;
                    if (isset($this->priceMap[$deviceServiceKey])) {
                        // Don't add device to services - it will be shown in hearing aid column
                        $regular = $this->priceMap[$deviceServiceKey]['R'];
                        $final = $this->priceMap[$deviceServiceKey][$ptCode] ?? $regular;
                        $discount = 0.0;
                        if ($ptCode !== 'R') {
                            $discount = $regular - $final;
                        }
                        $regularTotal += $regular;
                        $finalTotal += $final;
                        $discountTotal += $discount;
                    }
                }
                $haSummary = implode(', ', array_unique($deviceNames));
            }

            // Only create billing record if we have services OR hearing aids
            if (empty($serviceNames) && empty($haSummary)) {
                continue;
            }

            $rows[] = [
                'seq' => $seq++,
                'patient_id' => $patientId,
                'patient_name' => $this->composeName($p) ?: 'Unknown',
                'patient_type' => $patientTypeFull,
                'services' => implode(', ', $serviceNames),
                'hearing_aid' => $haSummary,
                'date' => $latestDate ? date('Y-m-d', strtotime($latestDate)) : date('Y-m-d'),
                'bill' => $regularTotal,
                'discount' => $discountTotal,
                'total' => $finalTotal,
            ];
        }

        // Always check DB billing as well to ensure we don't miss any records
        // This ensures that both session data and DB data are combined
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_billing')) {
            $dbRows = [];
            $billings = \App\Models\Billing::with('test')->get();
            if ($billings->count()) {
                $grouped = $billings->groupBy('patient_id');
                $seq = 1;
                foreach ($grouped as $patientId => $items) {
                    $patientId = (int)$patientId;
                    $p = $patients[$patientId] ?? [];
                    if (!$p && isset($dbPatients[$patientId])) { $p = $dbPatients[$patientId]; }
                    $patientTypeFull = $p['patient_type'] ?? ($items->first()->billing_patient_type ?? ($latestApptType[$patientId] ?? 'Regular'));
                    $serviceNames = [];
                    $hearingAidDetails = [];
                    $regularTotal = 0.0; $finalTotal = 0.0; $discountTotal = 0.0; $latestDate = null;
                    foreach ($items as $b) {
                        $regularTotal += (float)$b->billing_original_bill;
                        $finalTotal += (float)$b->billing_total_bill;
                        $discountTotal += (float)$b->billing_discount_bill;
                        $d = $b->billing_date; if ($d && (!$latestDate || strcmp($d, $latestDate) > 0)) { $latestDate = $d; }
                        if ($b->test && $b->test->test_type) { 
                            // Show "Hearing Aid Fitting" for the service, not the model name
                            if ($b->test->test_type === 'Hearing Aid Fitting') {
                                $serviceNames[] = 'Hearing Aid Fitting';
                            } elseif (strpos($b->test->test_type, 'Hearing Aid Device -') === 0) {
                                // Extract device model for hearing aid column
                                $deviceModel = str_replace('Hearing Aid Device - ', '', $b->test->test_type);
                                $hearingAidDetails[] = $deviceModel;
                            } else {
                                $serviceNames[] = $b->test->test_type;
                            }
                        }
                    }
                    $dbRows[] = [
                        'seq' => $seq++,
                        'patient_id' => $patientId,
                        'patient_name' => $this->composeName($p) ?: 'Unknown',
                        'patient_type' => $patientTypeFull,
                        'services' => implode(', ', array_unique($serviceNames)),
                        'hearing_aid' => implode(', ', array_unique($hearingAidDetails)),
                        'date' => $latestDate ? date('Y-m-d', strtotime($latestDate)) : date('Y-m-d'),
                        'bill' => $regularTotal,
                        'discount' => $discountTotal,
                        'total' => $finalTotal,
                    ];
                }
            }
            // Merge DB billing data with session data
            // Use DB data for patients not in session, or combine if both exist
            $existingPatientIds = array_column($rows, 'patient_id');
            if (!isset($grouped)) {
                $grouped = collect(); // Initialize as empty collection if not set
            }
            foreach ($grouped as $patientId => $items) {
                $patientId = (int)$patientId;
                
                // Skip if we already processed this patient from session data
                if (in_array($patientId, $existingPatientIds)) {
                    continue;
                }
                
                $p = $patients[$patientId] ?? [];
                if (!$p && isset($dbPatients[$patientId])) { $p = $dbPatients[$patientId]; }
                $patientTypeFull = $p['patient_type'] ?? ($items->first()->billing_patient_type ?? ($latestApptType[$patientId] ?? 'Regular'));
                $serviceNames = [];
                $hearingAidDetails = [];
                $regularTotal = 0.0; $finalTotal = 0.0; $discountTotal = 0.0; $latestDate = null;
                foreach ($items as $b) {
                    $regularTotal += (float)$b->billing_original_bill;
                    $finalTotal += (float)$b->billing_total_bill;
                    $discountTotal += (float)$b->billing_discount_bill;
                    $d = $b->billing_date; if ($d && (!$latestDate || strcmp($d, $latestDate) > 0)) { $latestDate = $d; }
                    if ($b->test && $b->test->test_type) { 
                        if ($b->test->test_type === 'Hearing Aid Fitting') {
                            $serviceNames[] = 'Hearing Aid Fitting';
                        } elseif (strpos($b->test->test_type, 'Hearing Aid Device -') === 0) {
                            $deviceModel = str_replace('Hearing Aid Device - ', '', $b->test->test_type);
                            $hearingAidDetails[] = $deviceModel;
                        } else {
                            $serviceNames[] = $b->test->test_type;
                        }
                    }
                }
                $rows[] = [
                    'seq' => count($rows) + 1,
                    'patient_id' => $patientId,
                    'patient_name' => $this->composeName($p) ?: 'Unknown',
                    'patient_type' => $patientTypeFull,
                    'services' => implode(', ', array_unique($serviceNames)),
                    'hearing_aid' => implode(', ', array_unique($hearingAidDetails)),
                    'date' => $latestDate ? date('Y-m-d', strtotime($latestDate)) : date('Y-m-d'),
                    'bill' => $regularTotal,
                    'discount' => $discountTotal,
                    'total' => $finalTotal,
                ];
            }
        }

        return view('admin.admin-billing', ['rows' => $rows]);
    }

    /**
     * Admin billing report - fetch all billing data for report
     */
    public function adminReport(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return redirect()->route('login');
        
        // Use the same data fetching logic as admin billing
        $patients = $this->loadPatientRecords();
        $svcAll = $request->session()->get('svc_session_records', []); // patient_id => [service => [records]]
        $haAll = $request->session()->get('ha_session_records', []); // patient_id => hearing aids

        $rows = [];
        $grouped = collect(); // Initialize as empty collection
        // Preload latest appointment patient_type per patient_id as fallback
        $latestApptType = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_appointment') && \Illuminate\Support\Facades\Schema::hasColumn('tbl_appointment','patient_id')) {
            try {
                $apptRows = \Illuminate\Support\Facades\DB::table('tbl_appointment')
                    ->select('patient_id','patient_type','appointment_date')
                    ->whereNotNull('patient_id')
                    ->orderByDesc('appointment_date')
                    ->get();
                foreach ($apptRows as $ar) {
                    $pid = (int)($ar->patient_id ?? 0);
                    if ($pid && !isset($latestApptType[$pid])) {
                        $latestApptType[$pid] = (string)($ar->patient_type ?? '');
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }
        // Preload DB patients for name fallback when file-based patient record is missing
        $dbPatients = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_patient')) {
            try {
                $dbRows = \Illuminate\Support\Facades\DB::table('tbl_patient')
                    ->select('patient_id','patient_firstname','patient_surname','patient_middlename','patient_type')
                    ->get();
                foreach ($dbRows as $dr) {
                    $pid = (int)($dr->patient_id ?? 0);
                    if ($pid) {
                        $dbPatients[$pid] = [
                            'first_name' => (string)($dr->patient_firstname ?? ''),
                            'last_name' => (string)($dr->patient_surname ?? ''),
                            'middle_name' => (string)($dr->patient_middlename ?? ''),
                            'patient_type' => (string)($dr->patient_type ?? ''),
                        ];
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        // Process session data first
        foreach ($svcAll as $patientId => $svcMap) {
            $patientId = (int)$patientId;
            $p = $patients[$patientId] ?? [];
            if (!$p && isset($dbPatients[$patientId])) { $p = $dbPatients[$patientId]; }
            $patientTypeFull = $p['patient_type'] ?? ($latestApptType[$patientId] ?? 'Regular');
            $serviceNames = [];
            $regularTotal = 0.0; $finalTotal = 0.0; $discountTotal = 0.0; $latestDate = null;
            foreach ($svcMap as $service => $records) {
                foreach ($records as $r) {
                    $regular = (float)($r['regular'] ?? 0);
                    $final = (float)($r['final'] ?? 0);
                    $discount = $regular - $final;
                    $regularTotal += $regular;
                    $finalTotal += $final;
                    $discountTotal += $discount;
                    if (isset($r['created_at']) && $r['created_at']) {
                        $d = date('Y-m-d', strtotime($r['created_at']));
                        if (!$latestDate || strcmp($d, $latestDate) > 0) { $latestDate = $d; }
                    }
                }
                $serviceNames[] = $service;
            }
            $hearingAid = '';
            if (isset($haAll[$patientId]) && is_array($haAll[$patientId]) && count($haAll[$patientId]) > 0) {
                $haList = [];
                foreach ($haAll[$patientId] as $ha) {
                    $brand = $ha['brand'] ?? '';
                    $model = $ha['model'] ?? '';
                    if ($brand && $model) { $haList[] = $brand . ' ' . $model; }
                }
                $hearingAid = implode(', ', $haList);
            }
            if ($regularTotal > 0 || $finalTotal > 0) {
                    // Try to get patient name from multiple sources
                    $patientName = 'Unknown';
                    if (!empty($p)) {
                        $patientName = $this->composeName($p);
                        \Log::info("Patient from JSON - ID: $patientId, Data: " . json_encode($p) . ", Name: $patientName");
                    }
                    if ($patientName === 'Unknown' && isset($dbPatients[$patientId])) {
                        $patientName = $this->composeName($dbPatients[$patientId]);
                        \Log::info("Patient from DB - ID: $patientId, Data: " . json_encode($dbPatients[$patientId]) . ", Name: $patientName");
                    }
                    if ($patientName === 'Unknown') {
                        \Log::info("No patient data found for ID: $patientId");
                        // Try direct database lookup as last resort
                        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_patient')) {
                            $directPatient = \Illuminate\Support\Facades\DB::table('tbl_patient')
                                ->where('patient_id', $patientId)
                                ->select('patient_firstname', 'patient_surname', 'patient_middlename')
                                ->first();
                            if ($directPatient) {
                                $patientName = trim(($directPatient->patient_firstname ?? '') . ' ' . ($directPatient->patient_surname ?? ''));
                                \Log::info("Direct DB lookup - ID: $patientId, Name: $patientName");
                            }
                        }
                    }
                    
                    $rows[] = [
                        'seq' => count($rows) + 1,
                        'patient_id' => $patientId,
                        'patient_name' => $patientName,
                        'patient_type' => $patientTypeFull,
                        'services' => implode(', ', array_unique($serviceNames)),
                        'hearing_aid' => $hearingAid,
                        'date' => $latestDate ?: date('Y-m-d'),
                        'bill' => $regularTotal,
                        'discount' => $discountTotal,
                        'total' => $finalTotal,
                    ];
            }
        }

        // Process DB billing data
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_billing')) {
            $dbRows = [];
            $billings = \App\Models\Billing::with('test')->get();
            if ($billings->count()) {
                $grouped = $billings->groupBy('patient_id');
                $existingPatientIds = array_column($rows, 'patient_id');
                foreach ($grouped as $patientId => $items) {
                    $patientId = (int)$patientId;
                    
                    // Skip if we already processed this patient from session data
                    if (in_array($patientId, $existingPatientIds)) {
                        continue;
                    }
                    
                    $p = $patients[$patientId] ?? [];
                    if (!$p && isset($dbPatients[$patientId])) { $p = $dbPatients[$patientId]; }
                    $patientTypeFull = $p['patient_type'] ?? ($items->first()->billing_patient_type ?? ($latestApptType[$patientId] ?? 'Regular'));
                    $serviceNames = [];
                    $hearingAidDetails = [];
                    $regularTotal = 0.0; $finalTotal = 0.0; $discountTotal = 0.0; $latestDate = null;
                    foreach ($items as $b) {
                        $regularTotal += (float)$b->billing_original_bill;
                        $finalTotal += (float)$b->billing_total_bill;
                        $discountTotal += (float)$b->billing_discount_bill;
                        $d = $b->billing_date; if ($d && (!$latestDate || strcmp($d, $latestDate) > 0)) { $latestDate = $d; }
                        if ($b->test && $b->test->test_type) { 
                            // Show "Hearing Aid Fitting" for the service, not the model name
                            if ($b->test->test_type === 'Hearing Aid Fitting') {
                                $serviceNames[] = 'Hearing Aid Fitting';
                            } elseif (strpos($b->test->test_type, 'Hearing Aid Device -') === 0) {
                                // Extract device model for hearing aid column
                                $deviceModel = str_replace('Hearing Aid Device - ', '', $b->test->test_type);
                                $hearingAidDetails[] = $deviceModel;
                            } else {
                                $serviceNames[] = $b->test->test_type;
                            }
                        }
                    }
                    // Try to get patient name from multiple sources
                    $patientName = 'Unknown';
                    if (!empty($p)) {
                        $patientName = $this->composeName($p);
                    }
                    if ($patientName === 'Unknown' && isset($dbPatients[$patientId])) {
                        $patientName = $this->composeName($dbPatients[$patientId]);
                    }
                    if ($patientName === 'Unknown') {
                        // Try direct database lookup as last resort
                        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_patient')) {
                            $directPatient = \Illuminate\Support\Facades\DB::table('tbl_patient')
                                ->where('patient_id', $patientId)
                                ->select('patient_firstname', 'patient_surname', 'patient_middlename')
                                ->first();
                            if ($directPatient) {
                                $patientName = trim(($directPatient->patient_firstname ?? '') . ' ' . ($directPatient->patient_surname ?? ''));
                            }
                        }
                    }
                    
                    $rows[] = [
                        'seq' => count($rows) + 1,
                        'patient_id' => $patientId,
                        'patient_name' => $patientName,
                        'patient_type' => $patientTypeFull,
                        'services' => implode(', ', array_unique($serviceNames)),
                        'hearing_aid' => implode(', ', array_unique($hearingAidDetails)),
                        'date' => $latestDate ? date('Y-m-d', strtotime($latestDate)) : date('Y-m-d'),
                        'bill' => $regularTotal,
                        'discount' => $discountTotal,
                        'total' => $finalTotal,
                    ];
                }
            }
        }

        // Debug: Log the data to see what we're getting
        \Log::info('Admin Report - Total rows: ' . count($rows));
        \Log::info('Admin Report - Patients from JSON: ' . count($patients));
        \Log::info('Admin Report - DB Patients: ' . count($dbPatients));
        \Log::info('Admin Report - Sample row: ' . json_encode($rows[0] ?? 'No rows'));
        
        // Check if we have any appointments with patient data
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_appointment')) {
            $appointments = \Illuminate\Support\Facades\DB::table('tbl_appointment')
                ->join('tbl_patient', 'tbl_appointment.patient_id', '=', 'tbl_patient.patient_id')
                ->select('tbl_appointment.patient_id', 'tbl_patient.patient_firstname', 'tbl_patient.patient_surname', 'tbl_patient.patient_middlename')
                ->limit(5)
                ->get();
            \Log::info('Sample appointments with patients: ' . json_encode($appointments));
        }
        
        return view('admin.admin-report-billing', ['rows' => $rows]);
    }

    public function destroy(Request $request, int $patientId)
    {
        $svcAll = $request->session()->get('svc_session_records', []);
        $haAll = $request->session()->get('ha_session_records', []);
        $changed = false;
        if (isset($svcAll[$patientId])) { unset($svcAll[$patientId]); $changed = true; }
        if (isset($haAll[$patientId])) { unset($haAll[$patientId]); $changed = true; }
        if ($changed) {
            $request->session()->put('svc_session_records', $svcAll);
            $request->session()->put('ha_session_records', $haAll);
            $request->session()->save();
        }
        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }
        return redirect()->route('staff.billing')->with('billing_deleted', 1);
    }

    protected function loadPatientRecords(): array
    {
        $path = storage_path('app/patient_records.json');
        if (!is_file($path)) return [];
        $json = @file_get_contents($path);
        $rows = json_decode($json, true);
        if (!is_array($rows)) return [];
        $map = [];
        foreach ($rows as $r) { $map[(int)($r['id'] ?? 0)] = $r; }
        return $map;
    }

    protected function mapPatientTypeCode(string $type): string
    {
        $t = strtolower(trim($type));
        if (str_contains($t, 'sen')) return 'SC';
        if ($t === 'pwd' || str_contains($t, 'dis')) return 'PWD';
        return 'R';
    }

    protected function composeName(array $p): string
    {
        $fn = trim((string)($p['first_name'] ?? ''));
        $mn = trim((string)($p['middle_name'] ?? ''));
        $ln = trim((string)($p['last_name'] ?? ''));
        return trim($fn.' '.($mn? $mn.' ': '').$ln) ?: 'Unknown';
    }
}
