<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BillingGenerator;

class ServiceResultSessionController extends Controller
{
    protected array $allowedServices = ['pta','tym','speech','oae','abr','assr','aided','play'];

    protected function svcKey(): string { return 'svc_session_records'; }
    protected function seqKey(): string { return 'svc_session_next_id'; }

    protected function validateService(string $service): string
    {
        $service = strtolower($service);
        if (!in_array($service, $this->allowedServices, true)) {
            abort(404, 'Unknown service');
        }
        return $service;
    }

    protected function readAll(Request $request): array
    {
        $all = $request->session()->get($this->svcKey(), []);
        return is_array($all) ? $all : [];
    }

    protected function writeAll(Request $request, array $all): void
    {
        $request->session()->put($this->svcKey(), $all);
    }

    protected function nextId(Request $request): int
    {
        $next = (int) $request->session()->get($this->seqKey(), 1);
        $request->session()->put($this->seqKey(), $next + 1);
        return $next;
    }

    /**
     * GET /staff/api/session/patient/{id}/services/{service}
     */
    public function index(Request $request, int $id, string $service)
    {
        $service = $this->validateService($service);
        $all = $this->readAll($request);
        $svcMap = $all[$id] ?? [];
        $list = $svcMap[$service] ?? [];
        usort($list, fn($a,$b) => strtotime($b['created_at'] ?? '0') <=> strtotime($a['created_at'] ?? '0'));
        return response()->json(['data' => array_values($list)]);
    }

    /**
     * POST /staff/api/session/patient/{id}/services/{service}
     * Accepts arbitrary payload per service and stores as-is plus metadata.
     */
    public function store(Request $request, int $id, string $service)
    {
        $service = $this->validateService($service);

        // Minimal validation per service (can be expanded)
        $data = $request->except(['_token']);

        // Add default date_taken if missing
        if (empty($data['date_taken'])) {
            $data['date_taken'] = date('Y-m-d');
        }

        $all = $this->readAll($request);
        $svcMap = $all[$id] ?? [];
        $list = $svcMap[$service] ?? [];

        $new = array_merge($data, [
            'id' => $this->nextId($request),
            'patient_id' => (int)$id,
            'service' => $service,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $list[] = $new;
        $svcMap[$service] = $list;
        $all[$id] = $svcMap;
        $this->writeAll($request, $all);
        $request->session()->save();

        return response()->json(['data' => $new], 201);
    }

    /**
     * DELETE /staff/api/session/patient/{id}/services/{service}/{resultId}
     */
    public function destroy(Request $request, int $id, string $service, int $resultId)
    {
        $service = $this->validateService($service);
        $all = $this->readAll($request);
        $svcMap = $all[$id] ?? [];
        $list = $svcMap[$service] ?? [];
        $before = count($list);
        $list = array_values(array_filter($list, fn($r) => (int)($r['id'] ?? 0) !== (int)$resultId));
        if (count($list) !== $before) {
            $svcMap[$service] = $list;
            $all[$id] = $svcMap;
            $this->writeAll($request, $all);
            $request->session()->save();
        }
        return response()->json(['status' => 'ok']);
    }

    /**
     * POST (form) /staff/patient-record/details/{id}/service/{service}/save
     * Saves then redirects to details.
     */
    public function storeAndRedirect(Request $request, int $id, string $service)
    {
        $service = $this->validateService($service);
        $data = $request->except(['_token']);
        if (empty($data['date_taken'])) { $data['date_taken'] = date('Y-m-d'); }

        $all = $this->readAll($request);
        $svcMap = $all[$id] ?? [];
        $list = $svcMap[$service] ?? [];

        $new = array_merge($data, [
            'id' => $this->nextId($request),
            'patient_id' => (int)$id,
            'service' => $service,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $list[] = $new;
        $svcMap[$service] = $list;
        $all[$id] = $svcMap;
        $this->writeAll($request, $all);
        $request->session()->save();

        // Persist to DB for all supported services (test + billing)
        if (in_array($service, ['oae','pta','tym','speech','abr','assr','aided','play'], true)) {
            try {
                // Attempt to resolve an appointment id for this patient (prefer confirmed; fallback to latest any status)
                $appointmentId = null;
                if (\Illuminate\Support\Facades\Schema::hasTable('tbl_appointment')) {
                    $hasStatus = \Illuminate\Support\Facades\Schema::hasColumn('tbl_appointment','status');
                    $hasPid    = \Illuminate\Support\Facades\Schema::hasColumn('tbl_appointment','patient_id');
                    // Prefer confirmed
                    $q1 = \Illuminate\Support\Facades\DB::table('tbl_appointment');
                    if ($hasStatus) { $q1->where('status','confirmed'); }
                    if ($hasPid) { $q1->where('patient_id', $id); }
                    $q1->whereNotNull('appointment_date')->orderByDesc('appointment_date');
                    $appointmentId = $q1->value('id');
                    if (!$appointmentId) {
                        // Fallback: latest appointment for this patient regardless of status
                        $q2 = \Illuminate\Support\Facades\DB::table('tbl_appointment');
                        if ($hasPid) { $q2->where('patient_id', $id); }
                        $q2->whereNotNull('appointment_date')->orderByDesc('appointment_date');
                        $appointmentId = $q2->value('id');
                    }
                }
                $record = [
                    'appointment_id' => $appointmentId,
                    'patient_id' => (int)$id,
                    'hearing_aid_id' => null,
                    'test_type' => $this->labelForService($service),
                    'test_note' => $this->notesForService($service, $data),
                    'test_result' => $this->summarizeService($service, $data),
                    'test_payload' => json_encode($data),
                    'test_date' => $data['date_taken'] ?? date('Y-m-d'),
                ];
                $test = \App\Models\Test::create($record);
                // If we found an appointment, mark it as completed after testing
                if ($appointmentId && \Illuminate\Support\Facades\Schema::hasColumn('tbl_appointment','status')) {
                    try {
                        \Illuminate\Support\Facades\DB::table('tbl_appointment')->where('id', $appointmentId)->update([
                            'status' => 'completed',
                            'confirmed_at' => now(),
                        ]);
                    } catch (\Throwable $e) { /* ignore */ }
                }
                // Delegate billing logic to service class
                (new BillingGenerator())->createForService($service, (int)$id, $data, $test);
            } catch (\Throwable $e) {
                // swallow DB errors; keep UX smooth
            }
        }

        return redirect()->route('staff.patient.record.details', ['id' => $id, 'svc_success' => $service]);
    }

    protected function summarizeOae(array $data): string
    {
        $parts = [];
        $map = [
            'oae_left_pass' => 'Left: Pass',
            'oae_left_refer' => 'Left: Refer',
            'oae_left_not_tested' => 'Left: Not tested',
            'oae_right_pass' => 'Right: Pass',
            'oae_right_refer' => 'Right: Refer',
            'oae_right_not_tested' => 'Right: Not tested',
        ];
        foreach ($map as $k => $label) {
            if (!empty($data[$k])) { $parts[] = $label; }
        }
        if (isset($data['oae_notes']) && trim((string)$data['oae_notes']) !== '') {
            $parts[] = 'Notes: '.trim((string)$data['oae_notes']);
        }
        return implode(' | ', $parts) ?: 'N/A';
    }

    protected function summarizeService(string $service, array $data): string
    {
        return match($service) {
            'oae' => $this->summarizeOae($data),
            'pta' => $this->summarizePta($data),
            'tym' => $this->summarizeTym($data),
            'speech' => $this->summarizeSpeech($data),
            'abr' => $this->summarizeAbr($data),
            'assr' => $this->summarizeAssr($data),
            // For aided/play, reuse speech summary if applicable
            'aided' => $this->summarizeSpeech($data),
            'play' => $this->summarizeSpeech($data),
            default => 'N/A'
        };
    }

    protected function labelForService(string $service): string
    {
        return match($service) {
            'oae' => 'Otoacoustic Emission (OAE)',
            'pta' => 'Pure Tone Audiometry',
            'tym' => 'Tympanometry',
            'speech' => 'Speech Audiometry',
            'abr' => 'Auditory Brainstem Response (ABR)',
            'assr' => 'Auditory Steady State Response (ASSR)',
            'aided' => 'Aided Testing',
            'play' => 'Play Audiometry',
            default => strtoupper($service)
        };
    }

    protected function notesForService(string $service, array $data): ?string
    {
        $key = match($service) {
            'oae' => 'oae_notes',
            'pta' => 'pta_notes',
            'tym' => 'tym_notes',
            'speech' => 'speech_notes',
            'abr' => 'abr_notes',
            'assr' => 'assr_notes', // ensure form uses this name; if not present returns null
            'aided' => 'speech_notes',
            'play' => 'speech_notes',
            default => null
        };
        if ($key && !empty($data[$key])) {
            $val = trim((string)$data[$key]);
            return $val === '' ? null : $val;
        }
        return null;
    }

    protected function summarizePta(array $data): string
    {
        // Average of first 3 AC thresholds for each ear (if provided) as quick summary
        $avg = function($arr) {
            $nums = array_filter(array_map(fn($v)=>is_numeric($v)? (float)$v : null, $arr));
            if (!$nums) return null;
            return round(array_sum($nums)/count($nums));
        };
        $right = $avg(($data['pta_right_ac'] ?? []));
        $left = $avg(($data['pta_left_ac'] ?? []));
        $parts = [];
        if ($right !== null) { $parts[] = 'Right PTA(avg AC): '.$right.' dB'; }
        if ($left !== null) { $parts[] = 'Left PTA(avg AC): '.$left.' dB'; }
        return $parts ? implode(' | ', $parts) : 'PTA recorded';
    }

    protected function summarizeTym(array $data): string
    {
        $right = $data['tym_right_type'] ?? '';
        $left = $data['tym_left_type'] ?? '';
        $parts = [];
        if ($right !== '') { $parts[] = 'Right: '.$right; }
        if ($left !== '') { $parts[] = 'Left: '.$left; }
        return $parts ? 'Tymp '.implode(' | ', $parts) : 'Tympanometry recorded';
    }

    protected function summarizeSpeech(array $data): string
    {
        $parts = [];
        if (!empty($data['speech_right_srt'])) $parts[] = 'R SRT: '.$data['speech_right_srt'];
        if (!empty($data['speech_right_sds'])) $parts[] = 'R SDS: '.$data['speech_right_sds'];
        if (!empty($data['speech_left_srt'])) $parts[] = 'L SRT: '.$data['speech_left_srt'];
        if (!empty($data['speech_left_sds'])) $parts[] = 'L SDS: '.$data['speech_left_sds'];
        return $parts ? implode(' | ', $parts) : 'Speech audiometry recorded';
    }

    protected function summarizeAbr(array $data): string
    {
        // Count number of non-empty wave entries for each ear
        $rKeys = ['abr_rn','abr_ri','abr_r3','abr_rv','abr_r13','abr_r35','abr_r15','abr_rvv'];
        $lKeys = ['abr_ln','abr_li','abr_l3','abr_lv','abr_l13','abr_l35','abr_l15','abr_lvv'];
        $countFilled = function($keys) use ($data) {
            $c=0; foreach ($keys as $k) { $vals = $data[$k] ?? []; if (is_array($vals)) { foreach ($vals as $v) if(trim((string)$v)!=='') $c++; } }
            return $c; };
        $r = $countFilled($rKeys);
        $l = $countFilled($lKeys);
        $parts = [];
        if ($r>0) $parts[] = 'Right waves: '.$r;
        if ($l>0) $parts[] = 'Left waves: '.$l;
        return $parts ? implode(' | ', $parts) : 'ABR recorded';
    }

    protected function summarizeAssr(array $data): string
    {
        $freqsR = ['assr_r_500','assr_r_1000','assr_r_2000','assr_r_4000'];
        $freqsL = ['assr_l_500','assr_l_1000','assr_l_2000','assr_l_4000'];
        $collect = function($keys) use ($data) {
            $vals = []; foreach ($keys as $k) if(!empty($data[$k])) $vals[] = $k.':'.$data[$k]; return $vals; };
        $rVals = $collect($freqsR);
        $lVals = $collect($freqsL);
        $parts = [];
        if ($rVals) $parts[] = 'R('.implode(',', $rVals).')';
        if ($lVals) $parts[] = 'L('.implode(',', $lVals).')';
        return $parts ? implode(' | ', $parts) : 'ASSR recorded';
    }
}
