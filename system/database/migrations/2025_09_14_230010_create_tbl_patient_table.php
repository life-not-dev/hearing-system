<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_patient', function (Blueprint $table) {
            $table->bigIncrements('patient_id');
            $table->string('patient_firstname', 100);
            $table->string('patient_surname', 100);
            $table->string('patient_middlename', 100)->nullable();
            $table->date('patient_birthdate')->nullable();
            $table->unsignedSmallInteger('patient_age')->nullable();
            $table->string('patient_gender', 20)->nullable();
            $table->string('patient_email', 150)->nullable()->index();
            $table->string('patient_contact_number', 30)->nullable()->index();
            $table->string('patient_address', 255)->nullable();
            $table->string('patient_referred_by', 150)->nullable();
            $table->text('patient_medical_history')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_patient');
    }
};
