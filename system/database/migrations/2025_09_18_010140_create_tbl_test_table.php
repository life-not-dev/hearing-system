<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_test', function (Blueprint $table) {
            $table->id('test_id');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('hearing_aid_id')->nullable();
            $table->string('test_type')->nullable();
            $table->text('test_note')->nullable();
            $table->string('test_result')->nullable(); // path to pdf or result label
            $table->date('test_date')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('tbl_appointment')->nullOnDelete();
            $table->foreign('hearing_aid_id')->references('hearing_aid_id')->on('tbl_hearing_aid')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_test');
    }
};
