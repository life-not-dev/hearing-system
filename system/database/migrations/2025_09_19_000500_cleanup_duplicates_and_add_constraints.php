<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Dedupe branches by (name + operating_hours)
        if (Schema::hasTable('tbl_branch')) {
            $branches = DB::table('tbl_branch')
                ->select('branch_id','branch_name','branch_operating_hours')
                ->orderBy('branch_id')
                ->get();
            $map = [];
            $keepByKey = [];
            foreach ($branches as $b) {
                $name = is_null($b->branch_name) ? '' : trim(mb_strtolower($b->branch_name));
                $hrs  = is_null($b->branch_operating_hours) ? '' : trim(mb_strtolower($b->branch_operating_hours));
                $key = $name."|".$hrs;
                if (!isset($keepByKey[$key])) {
                    $keepByKey[$key] = $b->branch_id; // keep the first (smallest id given order)
                } else {
                    $map[$b->branch_id] = $keepByKey[$key]; // duplicate -> canonical
                }
            }
            if (!empty($map)) {
                // Repoint appointments to canonical branch_ids
                foreach ($map as $dupId => $keepId) {
                    if (Schema::hasTable('tbl_appointment') && Schema::hasColumn('tbl_appointment','branch_id')) {
                        DB::table('tbl_appointment')->where('branch_id', $dupId)->update(['branch_id' => $keepId]);
                    }
                }
                // Delete duplicate branches
                DB::table('tbl_branch')->whereIn('branch_id', array_keys($map))->delete();
            }

            // Add unique constraint to prevent duplicates going forward
            Schema::table('tbl_branch', function (Blueprint $table) {
                $indexes = $this->listIndexes('tbl_branch');
                if (!in_array('uniq_branch_name_hours', $indexes, true)) {
                    try {
                        $table->unique(['branch_name','branch_operating_hours'], 'uniq_branch_name_hours');
                    } catch (\Throwable $e) { /* ignore if DB refuses due to existing dups */ }
                }
            });
        }

        // 2) Dedupe appointments by slot (date+time+branch)
        if (Schema::hasTable('tbl_appointment')) {
            $cols = DB::getSchemaBuilder()->getColumnListing('tbl_appointment');
            $hasBranchId = in_array('branch_id', $cols, true);
            $hasBranchTxt = in_array('branch', $cols, true);
            $hasStatus = in_array('status', $cols, true);

            // Load minimal fields
            $select = ['id', 'appointment_date', 'appointment_time', 'created_at'];
            if ($hasBranchId) $select[] = 'branch_id';
            if ($hasBranchTxt) $select[] = 'branch';
            if ($hasStatus) $select[] = 'status';
            if (in_array('patient_id', $cols, true)) $select[] = 'patient_id';

            $appts = DB::table('tbl_appointment')->select($select)->orderBy('appointment_date')->orderBy('appointment_time')->orderBy('id')->get();

            $slotKeep = [];
            $slotDupes = [];

            foreach ($appts as $a) {
                $date = (string) $a->appointment_date;
                $time = (string) $a->appointment_time;
                $bKey = $hasBranchId && $a->branch_id ? ('B#'.$a->branch_id) : ($hasBranchTxt ? ('T#'.strtolower(trim((string)($a->branch ?? '')))) : 'B#0');
                $key = $date.'|'.$time.'|'.$bKey;
                if (!isset($slotKeep[$key])) {
                    $slotKeep[$key] = $a; // keep first occurrence
                } else {
                    // prefer to keep a confirmed if the kept is not confirmed
                    if ($hasStatus && isset($a->status) && $a->status === 'confirmed') {
                        $kept = $slotKeep[$key];
                        if (!isset($kept->status) || $kept->status !== 'confirmed') {
                            $slotDupes[] = $kept->id; // previous becomes duplicate
                            $slotKeep[$key] = $a; // keep this confirmed one
                            continue;
                        }
                    }
                    // else, mark current as duplicate
                    $slotDupes[] = $a->id;
                }
            }

            if (!empty($slotDupes)) {
                DB::table('tbl_appointment')->whereIn('id', array_values(array_unique($slotDupes)))->delete();
            }

            // 3) Dedupe appointments by patient per day (one appointment/day/person)
            if (in_array('patient_id', $cols, true)) {
                $byPatientDay = DB::table('tbl_appointment')
                    ->select($select)
                    ->orderBy('appointment_date')->orderBy('appointment_time')->orderBy('id')
                    ->get();
                $keep = [];
                $dupes = [];
                foreach ($byPatientDay as $a) {
                    if (empty($a->patient_id)) continue; // skip null/empty
                    $key = $a->patient_id.'|'.(string)$a->appointment_date;
                    if (!isset($keep[$key])) {
                        $keep[$key] = $a;
                    } else {
                        $kept = $keep[$key];
                        // prefer confirmed over pending
                        $isConfA = $hasStatus && isset($a->status) && $a->status === 'confirmed';
                        $isConfK = $hasStatus && isset($kept->status) && $kept->status === 'confirmed';
                        if ($isConfA && !$isConfK) {
                            $dupes[] = $kept->id;
                            $keep[$key] = $a;
                        } else {
                            $dupes[] = $a->id;
                        }
                    }
                }
                if (!empty($dupes)) {
                    DB::table('tbl_appointment')->whereIn('id', array_values(array_unique($dupes)))->delete();
                }

                // Add unique index to enforce one appointment per day per patient
                Schema::table('tbl_appointment', function (Blueprint $table) {
                    $indexes = $this->listIndexes('tbl_appointment');
                    if (!in_array('uniq_patient_per_day', $indexes, true)) {
                        try {
                            $table->unique(['patient_id','appointment_date'], 'uniq_patient_per_day');
                        } catch (\Throwable $e) { /* ignore */ }
                    }
                });
            }

            // Add unique indexes for slot per branch and legacy branch text
            Schema::table('tbl_appointment', function (Blueprint $table) use ($hasBranchTxt, $hasBranchId) {
                $indexes = $this->listIndexes('tbl_appointment');
                if ($hasBranchId && !in_array('uniq_slot_branch', $indexes, true)) {
                    try { $table->unique(['appointment_date','appointment_time','branch_id'], 'uniq_slot_branch'); } catch (\Throwable $e) { /* ignore */ }
                }
                if ($hasBranchTxt && !in_array('uniq_slot_branch_text', $indexes, true)) {
                    try { $table->unique(['appointment_date','appointment_time','branch'], 'uniq_slot_branch_text'); } catch (\Throwable $e) { /* ignore */ }
                }
            });
        }
    }

    public function down(): void
    {
        // Drop unique indexes if present (do not restore deleted duplicates)
        if (Schema::hasTable('tbl_branch')) {
            Schema::table('tbl_branch', function (Blueprint $table) {
                try { $table->dropUnique('uniq_branch_name_hours'); } catch (\Throwable $e) { /* ignore */ }
            });
        }
        if (Schema::hasTable('tbl_appointment')) {
            Schema::table('tbl_appointment', function (Blueprint $table) {
                try { $table->dropUnique('uniq_patient_per_day'); } catch (\Throwable $e) { /* ignore */ }
                try { $table->dropUnique('uniq_slot_branch'); } catch (\Throwable $e) { /* ignore */ }
                try { $table->dropUnique('uniq_slot_branch_text'); } catch (\Throwable $e) { /* ignore */ }
            });
        }
    }

    // Helper to list index names (works on MySQL/SQLite)
    private function listIndexes(string $table): array
    {
        $driver = Schema::getConnection()->getDriverName();
        $indexes = [];
        try {
            if ($driver === 'mysql') {
                $dbName = DB::getDatabaseName();
                $rows = DB::select('SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$dbName, $table]);
                foreach ($rows as $r) { $indexes[] = $r->INDEX_NAME; }
            } else if ($driver === 'sqlite') {
                $rows = DB::select('PRAGMA index_list('.$table.')');
                foreach ($rows as $r) { $indexes[] = $r->name; }
            } else if ($driver === 'pgsql') {
                $rows = DB::select("SELECT indexname FROM pg_indexes WHERE schemaname = 'public' AND tablename = ?", [$table]);
                foreach ($rows as $r) { $indexes[] = $r->indexname; }
            }
        } catch (\Throwable $e) {}
        return $indexes;
    }
};
