<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_branch', function (Blueprint $table) {
            $table->bigIncrements('branch_id');
            $table->string('branch_name', 150);
            $table->string('branch_address', 255)->nullable();
            $table->string('branch_operating_hours', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_branch');
    }
};
