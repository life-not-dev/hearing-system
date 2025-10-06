<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('branch');
            }
        });

        // Backfill branch_id from users.branch if possible
        if (Schema::hasTable('tbl_branch')) {
            $users = DB::table('users')->select('id','branch','branch_id')->get();
            foreach ($users as $u) {
                if ($u->branch_id) continue;
                $name = $u->branch ? trim($u->branch) : null;
                if (!$name) continue;
                $bid = DB::table('tbl_branch')->where('branch_name', $name)->value('branch_id');
                if ($bid) {
                    DB::table('users')->where('id', $u->id)->update(['branch_id' => $bid]);
                }
            }
        }

        // Add FK if supported (non-sqlite)
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users','branch_id')) {
                    try { $table->foreign('branch_id')->references('branch_id')->on('tbl_branch')->nullOnDelete(); } catch (\Throwable $e) {}
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users','branch_id')) {
                if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                    try { $table->dropForeign(['branch_id']); } catch (\Throwable $e) {}
                }
                $table->dropColumn('branch_id');
            }
        });
    }
};
