<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tbl_patient')) return;
        Schema::create('tbl_patient', function (Blueprint $table) {
            $table->id('patient_id');
            $table->string('patient_firstname');
            $table->string('patient_surname');
            $table->string('patient_middlename')->nullable();
            $table->date('patient_birthdate')->nullable();
            $table->unsignedTinyInteger('patient_age')->nullable();
            $table->string('patient_gender', 10)->nullable();
            $table->string('patient_email')->nullable();
            $table->string('patient_contact_number', 30)->nullable();
            $table->string('patient_address')->nullable();
            $table->string('patient_referred_by')->nullable();
            $table->text('patient_medical_history')->nullable();
            $table->timestamps();
            $table->index(['patient_email']);
            $table->index(['patient_contact_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_patient');
    }
};
