<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HearingAidSessionController extends Controller
{
    /**
     * Session keys used for storage
     */
    protected function sessionKey(): string
    {
        return 'ha_session_records'; // array<int patientId, array<int, array>>
    }

    protected function sessionSeqKey(): string
    {
        return 'ha_session_next_id';
    }

    /**
     * Read map of patientId => [hearingAidRecords]
     * @return array<int, array<int, array<string,mixed>>>
     */
    protected function readAll(Request $request): array
    {
        $all = $request->session()->get($this->sessionKey(), []);
        return is_array($all) ? $all : [];
    }

    /**
     * Persist the full map
     * @param array<int, array<int, array<string,mixed>>> $all
     */
    protected function writeAll(Request $request, array $all): void
    {
        $request->session()->put($this->sessionKey(), $all);
    }

    /**
     * Generate the next in-session auto-increment id
     */
    protected function nextId(Request $request): int
    {
        $next = (int) $request->session()->get($this->sessionSeqKey(), 1);
        $request->session()->put($this->sessionSeqKey(), $next + 1);
        return $next;
    }

    /**
     * Helper: read patient full name from file-backed patient list
     */
    protected function lookupPatientName(int $patientId): string
    {
        $path = storage_path('app/patient_records.json');
        if (!is_file($path)) return '';
        $json = @file_get_contents($path);
        if (!$json) return '';
        $rows = json_decode($json, true);
        if (!is_array($rows)) return '';
        foreach ($rows as $r) {
            if ((int)($r['id'] ?? 0) === $patientId) {
                $fn = trim((string)($r['first_name'] ?? ''));
                $mn = trim((string)($r['middle_name'] ?? ''));
                $ln = trim((string)($r['last_name'] ?? ''));
                return trim($fn . ' ' . ($mn ? $mn . ' ' : '') . $ln);
            }
        }
        return '';
    }

    /**
     * GET /staff/api/session/patient/{id}/hearing-aids
     */
    public function index(Request $request, int $id)
    {
        $all = $this->readAll($request);
        $list = $all[$id] ?? [];
        // sort by created_at desc
        usort($list, function($a, $b){
            return strtotime($b['created_at'] ?? '0') <=> strtotime($a['created_at'] ?? '0');
        });
        return response()->json(['data' => array_values($list)]);
    }

    /**
     * POST /staff/api/session/patient/{id}/hearing-aids
     */
    public function store(Request $request, int $id)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'ear_side' => 'required|string|in:Left,Right,Both',
            'date_issued' => 'required|date',
        ]);

        // trim inputs
        foreach (['brand','model','ear_side','date_issued'] as $k) {
            if (isset($validated[$k]) && is_string($validated[$k])) {
                $validated[$k] = trim($validated[$k]);
            }
        }

        $all = $this->readAll($request);
        $list = $all[$id] ?? [];

        $new = [
            'id' => $this->nextId($request),
            'patient_id' => (int)$id,
            'patient_name' => $this->lookupPatientName((int)$id),
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'ear_side' => $validated['ear_side'],
            'date_issued' => $validated['date_issued'],
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $list[] = $new;
        $all[$id] = $list;
        $this->writeAll($request, $all);
        // Ensure session is persisted before response
        $request->session()->save();

        // Save to database for persistence after logout
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('tbl_test')) {
                $testRecord = [
                    'patient_id' => (int)$id,
                    'hearing_aid_id' => null,
                    'test_type' => 'Hearing Aid Fitting',
                    'test_note' => "Hearing Aid: {$validated['brand']} {$validated['model']} for {$validated['ear_side']} ear",
                    'test_result' => "Brand: {$validated['brand']}, Model: {$validated['model']}, Ear Side: {$validated['ear_side']}",
                    'test_payload' => json_encode($new),
                    'test_date' => $validated['date_issued'],
                ];
                \App\Models\Test::create($testRecord);
            }
        } catch (\Throwable $e) {
            // Log error but don't fail the request
            \Log::error('Failed to save hearing aid test to database: ' . $e->getMessage());
        }

        // Update appointment status to completed if found
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_appointment') && \Illuminate\Support\Facades\Schema::hasColumn('tbl_appointment','status')) {
            try {
                $appointment = \App\Models\Appointment::where('patient_id', $id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->orderByDesc('appointment_date')
                    ->first();
                if ($appointment) {
                    $appointment->update([
                        'status' => 'completed',
                        'confirmed_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) { 
                // ignore errors
            }
        }

        return response()->json(['data' => $new], 201);
    }

    /**
     * DELETE /staff/api/session/patient/{patientId}/hearing-aids/{hearingAidId}
     */
    public function destroy(Request $request, int $patientId, int $hearingAidId)
    {
        $all = $this->readAll($request);
        $list = $all[$patientId] ?? [];
        $before = count($list);
        $list = array_values(array_filter($list, function($r) use ($hearingAidId){
            return (int)($r['id'] ?? 0) !== (int)$hearingAidId;
        }));
        if (count($list) !== $before) {
            $all[$patientId] = $list;
            $this->writeAll($request, $all);
        }
        return response()->json(['status' => 'ok']);
    }

    /**
   
     * POST /staff/patient-record/details/{id}/hearing/save
     */
    public function storeAndRedirect(Request $request, int $id)
    {
        // Reuse the same validation rules
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'ear_side' => 'required|string|in:Left,Right,Both',
            'date_issued' => 'required|date',
        ]);

        // Call store() logic manually without returning JSON
        foreach (['brand','model','ear_side','date_issued'] as $k) {
            if (isset($validated[$k]) && is_string($validated[$k])) {
                $validated[$k] = trim($validated[$k]);
            }
        }

        $all = $this->readAll($request);
        $list = $all[$id] ?? [];

        $new = [
            'id' => $this->nextId($request),
            'patient_id' => (int)$id,
            'patient_name' => $this->lookupPatientName((int)$id),
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'ear_side' => $validated['ear_side'],
            'date_issued' => $validated['date_issued'],
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $list[] = $new;
        $all[$id] = $list;
        $this->writeAll($request, $all);
        // Ensure session is persisted before redirect
        $request->session()->save();

        // Save to database for persistence after logout
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('tbl_test')) {
                $testRecord = [
                    'patient_id' => (int)$id,
                    'hearing_aid_id' => null,
                    'test_type' => 'Hearing Aid Fitting',
                    'test_note' => "Hearing Aid: {$validated['brand']} {$validated['model']} for {$validated['ear_side']} ear",
                    'test_result' => "Brand: {$validated['brand']}, Model: {$validated['model']}, Ear Side: {$validated['ear_side']}",
                    'test_payload' => json_encode($new),
                    'test_date' => $validated['date_issued'],
                ];
                \App\Models\Test::create($testRecord);
            }
        } catch (\Throwable $e) {
            // Log error but don't fail the request
            \Log::error('Failed to save hearing aid test to database: ' . $e->getMessage());
        }

        // Update appointment status to completed if found and generate billing
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_appointment') && \Illuminate\Support\Facades\Schema::hasColumn('tbl_appointment','status')) {
            try {
                $appointment = \App\Models\Appointment::where('patient_id', $id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->orderByDesc('appointment_date')
                    ->first();
                if ($appointment) {
                    $appointment->update([
                        'status' => 'completed',
                        'confirmed_at' => now(),
                    ]);
                    
                    // Generate billing for hearing aid fitting - DUAL PRICING
                    try {
                        $testRecord = \App\Models\Test::where('patient_id', $id)
                            ->where('test_type', 'Hearing Aid Fitting')
                            ->orderByDesc('created_at')
                            ->first();
                        
                        if ($testRecord) {
                            $billingData = [
                                'date_taken' => $validated['date_issued'] ?? date('Y-m-d'),
                            ];
                            
                            // 1. Create billing for hearing aid fitting SERVICE
                            (new \App\Services\BillingGenerator())->createForService('hearing', (int)$id, $billingData, $testRecord);
                            
                            // 2. Create billing for hearing aid DEVICE based on model
                            $model = strtolower(str_replace(' ', '_', trim($validated['model'] ?? '')));
                            $deviceServiceKey = 'hearing_' . $model;
                            
                            // Check if this device model has pricing
                            $billingGenerator = new \App\Services\BillingGenerator();
                            if (method_exists($billingGenerator, 'getPriceMap')) {
                                $priceMap = $billingGenerator->getPriceMap();
                                if (isset($priceMap[$deviceServiceKey])) {
                                    // Create a separate test record for the device
                                    $deviceTestRecord = \App\Models\Test::create([
                                        'patient_id' => (int)$id,
                                        'hearing_aid_id' => null,
                                        'test_type' => 'Hearing Aid Device - ' . $validated['model'],
                                        'test_note' => "Hearing Aid Device: {$validated['brand']} {$validated['model']} for {$validated['ear_side']} ear",
                                        'test_result' => "Device: {$validated['brand']} {$validated['model']}, Ear Side: {$validated['ear_side']}",
                                        'test_payload' => json_encode([
                                            'device_type' => 'hearing_aid',
                                            'brand' => $validated['brand'],
                                            'model' => $validated['model'],
                                            'ear_side' => $validated['ear_side'],
                                            'date_issued' => $validated['date_issued'],
                                        ]),
                                        'test_date' => $validated['date_issued'],
                                    ]);
                                    
                                    // Create billing for the device
                                    (new \App\Services\BillingGenerator())->createForService($deviceServiceKey, (int)$id, $billingData, $deviceTestRecord);
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        \Log::error('Failed to generate billing for hearing aid test: ' . $e->getMessage());
                    }
                }
            } catch (\Throwable $e) { 
                // ignore errors
            }
        }

        return redirect()->route('staff.patient.record.details', ['id' => $id, 'ha_success' => 1]);
    }
}
