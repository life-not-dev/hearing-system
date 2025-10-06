<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_patient_record', function (Blueprint $table) {
            $table->id('patient_record_id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('billing_id')->nullable();
            $table->timestamp('patient_record_date_registered')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('patient_id')->on('tbl_patient')->nullOnDelete();
            $table->foreign('billing_id')->references('billing_id')->on('tbl_billing')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_patient_record');
    }
};
