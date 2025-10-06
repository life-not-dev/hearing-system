<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_appointment', function (Blueprint $table) {
            $cols = [
                'fname', 'email',
                'first_name','surname','middlename',
                'gender','birthdate','contact','address',
                'branch','services',
                'referred_by','medical_history',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('tbl_appointment', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_appointment', function (Blueprint $table) {
            // Minimal down: recreate as nullable strings/dates so rollback doesn't fail
            if (!Schema::hasColumn('tbl_appointment', 'fname')) $table->string('fname')->nullable();
            if (!Schema::hasColumn('tbl_appointment', 'email')) $table->string('email')->nullable();
            if (!Schema::hasColumn('tbl_appointment', 'first_name')) $table->string('first_name',100)->nullable();
            if (!Schema::hasColumn('tbl_appointment', 'surname')) $table->string('surname',100)->nullable();
            if (!Schema::hasColumn('tbl_appointment', 'middlename')) $table->string('middlename',100)->nullable();
            if (!Schema::hasColumn('tbl_appointment', 'gender')) $table->string('gender',10)->nullable();
            if (!Schema::hasColumn('tbl_appointment', 'birthdate')) $table->date('birthdate')->nullable();
            if (!Schema::hasColumn('tbl_appointment', 'contact')) $table->string('contact',20)->nullable();
            if (!Schema::hasColumn('tbl_appointment', 'address')) $table->string('address',255)->nullable();
            if (!Schema::hasColumn('tbl_appointment', 'branch')) $table->string('branch',120)->nullable();
            if (!Schema::hasColumn('tbl_appointment', 'services')) $table->string('services')->nullable();
            if (!Schema::hasColumn('tbl_appointment', 'referred_by')) $table->string('referred_by',150)->nullable();
            if (!Schema::hasColumn('tbl_appointment', 'medical_history')) $table->text('medical_history')->nullable();
        });
    }
};
