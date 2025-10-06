<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tbl_test') && !Schema::hasColumn('tbl_test','test_payload')) {
            Schema::table('tbl_test', function(Blueprint $table){
                $table->longText('test_payload')->nullable()->after('test_result');
            });
        }
    }
    public function down(): void
    {
        if (Schema::hasTable('tbl_test') && Schema::hasColumn('tbl_test','test_payload')) {
            Schema::table('tbl_test', function(Blueprint $table){
                $table->dropColumn('test_payload');
            });
        }
    }
};
