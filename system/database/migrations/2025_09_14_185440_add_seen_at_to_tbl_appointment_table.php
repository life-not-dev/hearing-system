<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('tbl_appointment')) return;
        Schema::table('tbl_appointment', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_appointment', 'seen_at')) {
                $table->timestamp('seen_at')->nullable()->after('appointment_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_appointment', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_appointment', 'seen_at')) {
                $table->dropColumn('seen_at');
            }
        });
    }
};
