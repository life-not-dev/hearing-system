<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_billing', function (Blueprint $table) {
            $table->id('billing_id');
            $table->unsignedBigInteger('test_id')->nullable();
            $table->date('billing_date')->nullable();
            $table->unsignedInteger('billing_original_bill')->default(0);
            $table->unsignedInteger('billing_discount_bill')->default(0);
            $table->unsignedInteger('billing_total_bill')->default(0);
            $table->string('billing_patient_type')->nullable();
            $table->timestamps();

            $table->foreign('test_id')->references('test_id')->on('tbl_test')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_billing');
    }
};
