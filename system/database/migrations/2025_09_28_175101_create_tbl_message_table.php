<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_message', function (Blueprint $table) {
            $table->id('message_id'); 
            $table->unsignedBigInteger('patient_id'); 
            $table->unsignedBigInteger('user_id'); 
            $table->string('sender_type'); 
            $table->unsignedBigInteger('receiver_id'); 
            $table->string('receiver_type'); 
            $table->longText('message_content'); 
            $table->timestamp('created_at')->nullable(); 
            $table->timestamp('read_at')->nullable(); 
            $table->unsignedBigInteger('appointment_id')->nullable(); 
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('appointment_id')->references('id')->on('tbl_appointment')->nullOnDelete();
            $table->foreign('branch_id')->references('branch_id')->on('tbl_branch')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_message');
    }
};
