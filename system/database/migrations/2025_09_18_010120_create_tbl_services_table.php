<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tbl_services')) return;
        Schema::create('tbl_services', function (Blueprint $table) {
            $table->id('service_id');
            $table->string('service_name');
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
