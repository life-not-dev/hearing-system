<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tbl_test') && !Schema::hasColumn('tbl_test','patient_id')) {
            Schema::table('tbl_test', function(Blueprint $table){
                $table->unsignedBigInteger('patient_id')->nullable()->after('appointment_id');
            });
        }
        if (Schema::hasTable('tbl_billing') && !Schema::hasColumn('tbl_billing','patient_id')) {
            Schema::table('tbl_billing', function(Blueprint $table){
                $table->unsignedBigInteger('patient_id')->nullable()->after('test_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tbl_test') && Schema::hasColumn('tbl_test','patient_id')) {
            Schema::table('tbl_test', function(Blueprint $table){ $table->dropColumn('patient_id'); });
        }
        if (Schema::hasTable('tbl_billing') && Schema::hasColumn('tbl_billing','patient_id')) {
            Schema::table('tbl_billing', function(Blueprint $table){ $table->dropColumn('patient_id'); });
        }
    }
};
