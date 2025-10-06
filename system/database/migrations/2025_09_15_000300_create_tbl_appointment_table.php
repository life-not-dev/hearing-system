<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_appointment', function (Blueprint $table) {
            $table->id();
            $table->string('fname'); // concatenated Firstname + Surname
            $table->string('services');
            $table->string('email');
            $table->time('appointment_time');
            $table->date('appointment_date');
            $table->timestamp('seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_appointment');
    }
};
