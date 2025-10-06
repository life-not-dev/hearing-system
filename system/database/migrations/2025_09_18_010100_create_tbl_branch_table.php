<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tbl_branch')) return;
        Schema::create('tbl_branch', function (Blueprint $table) {
            $table->id('branch_id');
            $table->string('branch_name');
            $table->string('branch_address')->nullable();
            $table->string('branch_operating_hours')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_branch');
    }
};
