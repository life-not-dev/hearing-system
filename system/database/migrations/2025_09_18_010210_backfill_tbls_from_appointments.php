<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tbl_appointment')) return;

        // Backfill branches
        if (Schema::hasColumn('tbl_appointment', 'branch') && Schema::hasTable('tbl_branch')) {
            $branches = DB::table('tbl_appointment')
                ->select('branch')
                ->whereNotNull('branch')
                ->where('branch', '!=', '')
                ->distinct()->pluck('branch');
            foreach ($branches as $name) {
                $exists = DB::table('tbl_branch')->where('branch_name', $name)->exists();
                if (!$exists) {
                    DB::table('tbl_branch')->insert([
                        'branch_name' => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Backfill services
        if (Schema::hasColumn('tbl_appointment', 'services') && Schema::hasTable('tbl_services')) {
            $services = DB::table('tbl_appointment')
                ->select('services')
                ->whereNotNull('services')
                ->where('services', '!=', '')
                ->distinct()->pluck('services');
            foreach ($services as $svc) {
                $exists = DB::table('tbl_services')->where('service_name', $svc)->exists();
                if (!$exists) {
                    DB::table('tbl_services')->insert([
                        'service_name' => $svc,
                        'service_price' => 0,
                        'service_status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Backfill patients from appointment details
        if (Schema::hasTable('tbl_patient')) {
            $apps = DB::table('tbl_appointment')->select([
                'id','first_name','surname','middlename','birthdate','gender','email','contact','address','referred_by','medical_history'
            ])->get();
            foreach ($apps as $a) {
                // lookup order: email, contact, composite (name+birthdate)
                $patientId = null;
                if (!empty($a->email)) {
                    $patientId = DB::table('tbl_patient')->where('patient_email', $a->email)->value('patient_id');
                }
                if (!$patientId && !empty($a->contact)) {
                    $patientId = DB::table('tbl_patient')->where('patient_contact_number', $a->contact)->value('patient_id');
                }
                if (!$patientId) {
                    $patientId = DB::table('tbl_patient')
                        ->where('patient_firstname', $a->first_name ?? '')
                        ->where('patient_surname', $a->surname ?? '')
                        ->when($a->birthdate, function($q) use ($a){ $q->where('patient_birthdate', $a->birthdate); })
                        ->value('patient_id');
                }
                if (!$patientId) {
                    $age = null;
                    if (!empty($a->birthdate)) {
                        try {
                            $age = \Carbon\Carbon::parse($a->birthdate)->age; // int
                            if (!is_int($age)) { $age = (int) floor($age); }
                            if ($age < 0 || $age > 120) { $age = null; }
                        } catch (\Throwable $e) { $age = null; }
                    }
                    $patientId = DB::table('tbl_patient')->insertGetId([
                        'patient_firstname' => $a->first_name ?? '',
                        'patient_surname' => $a->surname ?? '',
                        'patient_middlename' => $a->middlename ?? null,
                        'patient_birthdate' => $a->birthdate ?? null,
                        'patient_age' => $age,
                        'patient_gender' => $a->gender ?? null,
                        'patient_email' => $a->email ?? null,
                        'patient_contact_number' => $a->contact ?? null,
                        'patient_address' => $a->address ?? null,
                        'patient_referred_by' => $a->referred_by ?? null,
                        'patient_medical_history' => $a->medical_history ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                // link appointment -> patient
                if (!Schema::hasColumn('tbl_appointment', 'patient_id')) continue;
                DB::table('tbl_appointment')->where('id', $a->id)->update([
                    'patient_id' => $patientId,
                ]);
            }
        }

        // Link branch_id/service_id on appointments
        if (Schema::hasColumn('tbl_appointment', 'branch') && Schema::hasColumn('tbl_appointment', 'branch_id')) {
            $branchMap = DB::table('tbl_branch')->pluck('branch_id', 'branch_name');
            $apps = DB::table('tbl_appointment')->select('id','branch')->get();
            foreach ($apps as $a) {
                if ($a->branch && isset($branchMap[$a->branch])) {
                    DB::table('tbl_appointment')->where('id', $a->id)->update(['branch_id' => $branchMap[$a->branch]]);
                }
            }
        }
        if (Schema::hasColumn('tbl_appointment', 'services') && Schema::hasColumn('tbl_appointment', 'service_id')) {
            $svcMap = DB::table('tbl_services')->pluck('service_id', 'service_name');
            $apps = DB::table('tbl_appointment')->select('id','services')->get();
            foreach ($apps as $a) {
                if ($a->services && isset($svcMap[$a->services])) {
                    DB::table('tbl_appointment')->where('id', $a->id)->update(['service_id' => $svcMap[$a->services]]);
                }
            }
        }
    }

    public function down(): void
    {
        // Non-destructive: do nothing on rollback to preserve inserted data
    }
};
