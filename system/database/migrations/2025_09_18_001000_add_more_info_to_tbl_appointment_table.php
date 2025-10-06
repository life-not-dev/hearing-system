<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tbl_appointment')) return;
        Schema::table('tbl_appointment', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_appointment', 'first_name')) {
                $table->string('first_name', 100)->nullable()->after('id');
            }
            if (!Schema::hasColumn('tbl_appointment', 'surname')) {
                $table->string('surname', 100)->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('tbl_appointment', 'middlename')) {
                $table->string('middlename', 100)->nullable()->after('surname');
            }
            if (!Schema::hasColumn('tbl_appointment', 'referred_by')) {
                $table->string('referred_by', 150)->nullable()->after('branch');
            }
            if (!Schema::hasColumn('tbl_appointment', 'purpose')) {
                $table->string('purpose', 150)->nullable()->after('referred_by');
            }
            if (!Schema::hasColumn('tbl_appointment', 'medical_history')) {
                $table->text('medical_history')->nullable()->after('purpose');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tbl_appointment')) return;
        Schema::table('tbl_appointment', function (Blueprint $table) {
            foreach (['medical_history','purpose','referred_by','middlename','surname','first_name'] as $col) {
                if (Schema::hasColumn('tbl_appointment', $col)) $table->dropColumn($col);
            }
        });
    }
};
