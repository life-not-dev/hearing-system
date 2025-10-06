<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_services', function (Blueprint $table) {
            $table->bigIncrements('service_id');
            $table->string('service_name', 150);
            $table->unsignedInteger('service_price')->default(0);
            $table->enum('service_status', ['active','inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_services');
    }
};
