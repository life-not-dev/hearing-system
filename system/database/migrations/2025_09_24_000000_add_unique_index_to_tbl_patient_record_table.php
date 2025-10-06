<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tbl_patient_record')) {
            // Try to add; ignore if exists
            try {
                Schema::table('tbl_patient_record', function (Blueprint $table) {
                    $table->unique('patient_id', 'tbl_patient_record_patient_id_unique');
                });
            } catch (Throwable $e) {
                // silently ignore
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tbl_patient_record')) {
            try {
                Schema::table('tbl_patient_record', function (Blueprint $table) {
                    $table->dropUnique('tbl_patient_record_patient_id_unique');
                });
            } catch (Throwable $e) {
                // ignore
            }
        }
    }
};
