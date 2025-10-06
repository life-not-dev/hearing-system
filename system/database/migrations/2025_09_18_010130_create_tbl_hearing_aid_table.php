<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_hearing_aid', function (Blueprint $table) {
            $table->id('hearing_aid_id');
            $table->string('hearing_aid_brand');
            $table->string('hearing_aid_model');
            $table->unsignedInteger('hearing_aid_price')->default(0);
            $table->date('hearing_aid_date_issued')->nullable();
            $table->enum('hearing_aid_ear_side', ['left','right','both'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_hearing_aid');
    }
};
