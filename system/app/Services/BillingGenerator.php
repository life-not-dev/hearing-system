<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Test;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BillingGenerator
{
    protected array $priceMap = [
        'pta' => ['R' => 1000, 'SC' => 800, 'PWD' => 800],
        'speech' => ['R' => 625, 'SC' => 500, 'PWD' => 500],
        'tym' => ['R' => 635, 'SC' => 500, 'PWD' => 500],
        'abr' => ['R' => 7500, 'SC' => 6000, 'PWD' => 6000],
        'assr' => ['R' => 7500, 'SC' => 6000, 'PWD' => 6000],
        'oae' => ['R' => 500, 'SC' => 500, 'PWD' => 500],
        // Extended services per requirements
        'aided' => ['R' => 1000, 'SC' => 800, 'PWD' => 800],
        // Generic hearing aid fitting service
        'hearing' => ['R' => 1000, 'SC' => 800, 'PWD' => 800],
        // Play Audiometry: no senior discount; PWD gets 20% (2960)
        'play' => ['R' => 3700, 'SC' => 3700, 'PWD' => 2960],
        // Hearing Aid Models with 20% discount for SC and PWD
        'hearing_tmaxx600_chargable' => ['R' => 105000, 'SC' => 84000, 'PWD' => 84000],
        'hearing_tmaxx600_battery' => ['R' => 65000, 'SC' => 52000, 'PWD' => 52000],
        'hearing_stridep500_chargable' => ['R' => 120000, 'SC' => 96000, 'PWD' => 96000],
        'hearing_stridep500_battery' => ['R' => 80000, 'SC' => 64000, 'PWD' => 64000],
    ];

    public function getPriceMap(): array
    {
        return $this->priceMap;
    }

    public function createForService(string $service, int $patientId, array $data, Test $test): void
    {
        if (!Schema::hasTable('tbl_billing')) return;
        $patientType = $this->resolvePatientType($patientId);
        [$regular, $final, $discount] = $this->computeAmounts($service, $patientType);
        Billing::create([
            'test_id' => $test->test_id,
            'patient_id' => $patientId,
            'billing_date' => $data['date_taken'] ?? date('Y-m-d'),
            'billing_original_bill' => $regular,
            'billing_discount_bill' => $discount,
            'billing_total_bill' => $final,
            'billing_patient_type' => $patientType,
        ]);
    }

    protected function resolvePatientType(int $patientId): string
    {
        // 1) Try file-backed patient record
        $path = storage_path('app/patient_records.json');
        if (is_file($path)) {
            $json = @file_get_contents($path);
            $rows = $json ? json_decode($json, true) : [];
            if (is_array($rows)) {
                foreach ($rows as $r) {
                    if ((int)($r['id'] ?? 0) === $patientId) {
                        return $r['patient_type'] ?? 'Regular';
                    }
                }
            }
        }
        // 2) Fallback to latest appointment patient_type if available
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('tbl_appointment') && \Illuminate\Support\Facades\Schema::hasColumn('tbl_appointment','patient_id')) {
                $pt = \Illuminate\Support\Facades\DB::table('tbl_appointment')
                    ->where('patient_id', $patientId)
                    ->orderByDesc('appointment_date')
                    ->value('patient_type');
                if (is_string($pt) && trim($pt) !== '') { return $pt; }
            }
        } catch (\Throwable $e) { /* ignore */ }
        return 'Regular';
    }

    protected function computeAmounts(string $service, string $patientType): array
    {
        $map = $this->priceMap[$service] ?? null;
        if (!$map) return [0,0,0];
        $ptCode = 'R';
        $t = strtolower($patientType);
        if (str_contains($t,'sen')) $ptCode='SC';
        elseif ($t==='pwd' || str_contains($t,'dis')) $ptCode='PWD';
        $regular = $map['R'];
        $final = $map[$ptCode] ?? $regular;
        $discount = 0;
        if ($service !== 'oae' && $ptCode !== 'R') { $discount = $regular - $final; }
        return [$regular, $final, $discount];
    }
}
