<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_appointment', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_appointment', 'patient_id')) {
                $table->unsignedBigInteger('patient_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('tbl_appointment', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('patient_id');
            }
            if (!Schema::hasColumn('tbl_appointment', 'service_id')) {
                $table->unsignedBigInteger('service_id')->nullable()->after('branch_id');
            }
        });

        // On SQLite, adding FKs with ALTER TABLE is not supported. Guard by driver.
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('tbl_appointment', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_appointment', 'patient_id')) {
                    $table->foreign('patient_id')->references('patient_id')->on('tbl_patient')->nullOnDelete();
                }
                if (Schema::hasColumn('tbl_appointment', 'branch_id')) {
                    $table->foreign('branch_id')->references('branch_id')->on('tbl_branch')->nullOnDelete();
                }
                if (Schema::hasColumn('tbl_appointment', 'service_id')) {
                    $table->foreign('service_id')->references('service_id')->on('tbl_services')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('tbl_appointment', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_appointment', 'service_id')) {
                // Drop FK only if non-sqlite
                if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                    $table->dropForeign(['service_id']);
                }
                $table->dropColumn('service_id');
            }
            if (Schema::hasColumn('tbl_appointment', 'branch_id')) {
                if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                    $table->dropForeign(['branch_id']);
                }
                $table->dropColumn('branch_id');
            }
            if (Schema::hasColumn('tbl_appointment', 'patient_id')) {
                if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                    $table->dropForeign(['patient_id']);
                }
                $table->dropColumn('patient_id');
            }
        });
    }
};
