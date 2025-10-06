<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_user', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('user_fullname');
            $table->string('user_email')->unique();
            $table->string('user_password');
            $table->string('user_contact_number', 30)->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('patient_record_id')->nullable();
            $table->timestamps();
        });

        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('tbl_user', function (Blueprint $table) {
                $table->foreign('branch_id')->references('branch_id')->on('tbl_branch')->nullOnDelete();
                $table->foreign('patient_record_id')->references('patient_record_id')->on('tbl_patient_record')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('tbl_user', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_user', 'branch_id') && Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->dropForeign(['branch_id']);
            }
            if (Schema::hasColumn('tbl_user', 'patient_record_id') && Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->dropForeign(['patient_record_id']);
            }
        });
        Schema::dropIfExists('tbl_user');
    }
};
