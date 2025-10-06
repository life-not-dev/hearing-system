<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_notification', function (Blueprint $table) {
            $table->id('notification_id');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->text('notification_message')->nullable();
            $table->dateTime('send_date_time')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('tbl_appointment')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_notification');
    }
};
