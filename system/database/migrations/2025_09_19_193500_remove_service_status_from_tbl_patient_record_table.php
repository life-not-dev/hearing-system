<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('tbl_patient_record', 'service_status')) {
            Schema::table('tbl_patient_record', function (Blueprint $table) {
                $table->dropColumn('service_status');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('tbl_patient_record', 'service_status')) {
            Schema::table('tbl_patient_record', function (Blueprint $table) {
                $table->string('service_status')->nullable();
            });
        }
    }
};
