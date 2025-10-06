<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tbl_appointment')) return;
        Schema::table('tbl_appointment', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_appointment', 'status')) {
                $table->string('status', 20)->default('pending')->after('seen_at');
            }
            if (!Schema::hasColumn('tbl_appointment', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('tbl_appointment', 'canceled_at')) {
                $table->timestamp('canceled_at')->nullable()->after('confirmed_at');
            }
            if (!Schema::hasColumn('tbl_appointment', 'patient_type')) {
                $table->string('patient_type', 40)->nullable()->after('canceled_at');
            }
            if (!Schema::hasColumn('tbl_appointment', 'branch')) {
                $table->string('branch', 120)->nullable()->after('patient_type');
            }
            if (!Schema::hasColumn('tbl_appointment', 'gender')) {
                $table->string('gender', 10)->nullable()->after('branch');
            }
            if (!Schema::hasColumn('tbl_appointment', 'birthdate')) {
                $table->date('birthdate')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('tbl_appointment', 'contact')) {
                $table->string('contact', 20)->nullable()->after('birthdate');
            }
            if (!Schema::hasColumn('tbl_appointment', 'address')) {
                $table->string('address', 255)->nullable()->after('contact');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_appointment', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_appointment', 'address')) $table->dropColumn('address');
            if (Schema::hasColumn('tbl_appointment', 'contact')) $table->dropColumn('contact');
            if (Schema::hasColumn('tbl_appointment', 'birthdate')) $table->dropColumn('birthdate');
            if (Schema::hasColumn('tbl_appointment', 'gender')) $table->dropColumn('gender');
            if (Schema::hasColumn('tbl_appointment', 'branch')) $table->dropColumn('branch');
            if (Schema::hasColumn('tbl_appointment', 'patient_type')) $table->dropColumn('patient_type');
            if (Schema::hasColumn('tbl_appointment', 'canceled_at')) $table->dropColumn('canceled_at');
            if (Schema::hasColumn('tbl_appointment', 'confirmed_at')) $table->dropColumn('confirmed_at');
            if (Schema::hasColumn('tbl_appointment', 'status')) $table->dropColumn('status');
        });
    }
};
