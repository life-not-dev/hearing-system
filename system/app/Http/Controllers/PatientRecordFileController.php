<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientRecordFileController extends Controller
{
    /**
     * Path to the JSON storage file under storage/app
     */
    protected function storagePath(): string
    {
        return storage_path('app/patient_records.json');
    }

    /**
     * Safely read all patient records from JSON file
     * @return array<int, array<string,mixed>>
     */
    protected function readAll(): array
    {
        $path = $this->storagePath();
        if (!file_exists($path)) {
            return [];
        }
        $json = @file_get_contents($path);
        if ($json === false || $json === '') {
            return [];
        }
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return [];
        }
        // Normalize ids to int
        foreach ($data as &$row) {
            if (isset($row['id'])) $row['id'] = (int) $row['id'];
        }
        return $data;
    }

    /**
     * Safely write all patient records to JSON file
     * @param array<int, array<string,mixed>> $rows
     */
    protected function writeAll(array $rows): void
    {
        $path = $this->storagePath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        // Use LOCK_EX to avoid concurrent write corruption
        @file_put_contents($path, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    }

    /**
     * GET /staff/api/patient-records
     */
    public function index(Request $request)
    {
        $rows = $this->readAll();
        // Sort by id ascending for consistency
        usort($rows, function ($a, $b) {
            return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
        });
        return response()->json(['data' => $rows]);
    }

    /**
     * POST /staff/api/patient-records
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|string|in:Male,Female,Other',
            'birthday' => 'required|date',
            'date_registered' => 'required|date',
        ]);

        // Trim strings
        foreach (['first_name','middle_name','last_name','gender','birthday','date_registered'] as $k) {
            if (isset($validated[$k]) && is_string($validated[$k])) {
                $validated[$k] = trim($validated[$k]);
            }
        }

        $rows = $this->readAll();
        $maxId = 0;
        foreach ($rows as $r) { $maxId = max($maxId, (int)($r['id'] ?? 0)); }
        $nextId = $maxId + 1;

        $new = [
            'id' => $nextId,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? '',
            'last_name' => $validated['last_name'],
            'gender' => $validated['gender'],
            'birthday' => $validated['birthday'],
            'date_registered' => $validated['date_registered'],
        ];

        $rows[] = $new;
        $this->writeAll($rows);

        return response()->json(['data' => $new], 201);
    }

    /**
     * DELETE /staff/api/patient-records/{id}
     */
    public function destroy(int $id)
    {
        $rows = $this->readAll();
        $before = count($rows);
        $rows = array_values(array_filter($rows, function ($r) use ($id) {
            return (int)($r['id'] ?? 0) !== (int)$id;
        }));
        if (count($rows) !== $before) {
            $this->writeAll($rows);
        }
        return response()->json(['status' => 'ok']);
    }

    /**
     * GET /staff/api/patient-records/{id}
     */
    public function show(int $id)
    {
        $rows = $this->readAll();
        foreach ($rows as $r) {
            if ((int)($r['id'] ?? 0) === (int)$id) {
                return response()->json(['data' => $r]);
            }
        }
        return response()->json(['message' => 'Not found'], 404);
    }

    /**
     * Path to the hearing aid JSON storage file under storage/app
     */
    protected function hearingAidStoragePath(): string
    {
        return storage_path('app/hearing_aid_records.json');
    }

    /**
     * Safely read all hearing aid records from JSON file
     * @return array<int, array<string,mixed>>
     */
    protected function readAllHearingAids(): array
    {
        $path = $this->hearingAidStoragePath();
        if (!file_exists($path)) {
            return [];
        }
        $json = @file_get_contents($path);
        if ($json === false || $json === '') {
            return [];
        }
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return [];
        }
        return $data;
    }

    /**
     * Safely write all hearing aid records to JSON file
     * @param array<int, array<string,mixed>> $rows
     */
    protected function writeAllHearingAids(array $rows): void
    {
        $path = $this->hearingAidStoragePath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        // Use LOCK_EX to avoid concurrent write corruption
        @file_put_contents($path, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    }

    /**
     * GET /staff/api/patient-records/{id}/hearing-aids
     */
    public function getHearingAids(int $id)
    {
        $hearingAids = $this->readAllHearingAids();
        $patientHearingAids = array_filter($hearingAids, function ($ha) use ($id) {
            return (int)($ha['patient_id'] ?? 0) === (int)$id;
        });
        
        // Sort by created date descending
        usort($patientHearingAids, function ($a, $b) {
            return strtotime($b['created_at'] ?? '0') <=> strtotime($a['created_at'] ?? '0');
        });

        return response()->json(['data' => array_values($patientHearingAids)]);
    }

    /**
     * POST /staff/api/patient-records/{id}/hearing-aids
     */
    public function storeHearingAid(Request $request, int $id)
    {
        // First, verify patient exists
        $patients = $this->readAll();
        $patient = null;
        foreach ($patients as $p) {
            if ((int)($p['id'] ?? 0) === (int)$id) {
                $patient = $p;
                break;
            }
        }
        
        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'ear_side' => 'required|string|in:Left,Right,Both',
            'date_issued' => 'required|date',
        ]);

        // Trim strings
        foreach (['brand', 'model', 'ear_side', 'date_issued'] as $k) {
            if (isset($validated[$k]) && is_string($validated[$k])) {
                $validated[$k] = trim($validated[$k]);
            }
        }

        $hearingAids = $this->readAllHearingAids();
        $maxId = 0;
        foreach ($hearingAids as $ha) { 
            $maxId = max($maxId, (int)($ha['id'] ?? 0)); 
        }
        $nextId = $maxId + 1;

        // Get patient name
        $patientName = trim(($patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''));

        $new = [
            'id' => $nextId,
            'patient_id' => (int)$id,
            'patient_name' => $patientName,
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'ear_side' => $validated['ear_side'],
            'date_issued' => $validated['date_issued'],
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $hearingAids[] = $new;
        $this->writeAllHearingAids($hearingAids);

        return response()->json(['data' => $new], 201);
    }

    /**
     * DELETE /staff/api/patient-records/{patientId}/hearing-aids/{hearingAidId}
     */
    public function destroyHearingAid(int $patientId, int $hearingAidId)
    {
        $hearingAids = $this->readAllHearingAids();
        $before = count($hearingAids);
        
        $hearingAids = array_values(array_filter($hearingAids, function ($ha) use ($patientId, $hearingAidId) {
            return !((int)($ha['patient_id'] ?? 0) === (int)$patientId && (int)($ha['id'] ?? 0) === (int)$hearingAidId);
        }));
        
        if (count($hearingAids) !== $before) {
            $this->writeAllHearingAids($hearingAids);
        }
        
        return response()->json(['status' => 'ok']);
    }
}
