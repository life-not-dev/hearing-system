<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Models\HearingAid;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed default admin user
        $adminEmail = 'junelmicabalo19@gmail.com';
        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'name' => 'System Administrator',
                'email' => $adminEmail,
                'password' => 'Junelmicabalo@29', // hashed automatically via cast
                'role' => 'admin'
            ]);
        }

        // Seed tbl_branch (normalized branches) if empty
        if (DB::table('tbl_branch')->count() === 0) {
            DB::table('tbl_branch')->insert([
                [
                    'branch_name' => 'CDO Branch',
                    'branch_address' => 'Cagayan de Oro City',
                    'branch_operating_hours' => '08:00-17:00',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'branch_name' => 'Davao City Branch',
                    'branch_address' => 'Davao City',
                    'branch_operating_hours' => '08:00-17:00',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'branch_name' => 'Butuan City Branch',
                    'branch_address' => 'Butuan City',
                    'branch_operating_hours' => '08:00-17:00',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // Seed tbl_services (normalized services) if empty. Use labels that the booking form uses.
        if (DB::table('tbl_services')->count() === 0) {
            $svcNames = [
                'OAE',
                'ABR',
                'ASSR',
                'PTA',
                'Audiometry',
                'Speech Test',
                'Tympanometry',
                'Play Audiometry',
                'Hearing Aid Fitting',
                'Aided Testing',
            ];
            $rows = array_map(function ($n) {
                return [
                    'service_name' => $n,
                    'service_price' => 0,
                    'service_status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $svcNames);
            DB::table('tbl_services')->insert($rows);
        }

        // Seed Services if empty
        if (Service::count() === 0) {
            $services = [
                ['name' => 'PTA - Pureton Audiometry', 'price' => 1000, 'status' => 'active'],
                ['name' => 'Speech Audiometry', 'price' => 625, 'status' => 'active'],
                ['name' => 'Tympanometry', 'price' => 635, 'status' => 'active'],
                ['name' => 'ABR - Auditory Brain Response', 'price' => 7500, 'status' => 'active'],
                ['name' => 'ASSR - Auditory State Steady Response', 'price' => 7500, 'status' => 'active'],
                ['name' => 'OAE - Oto Acoustic with Emession', 'price' => 500, 'status' => 'active'],
                ['name' => 'Aided Testing', 'price' => 1000, 'status' => 'active'],
                ['name' => 'Play Audiometry', 'price' => 3700, 'status' => 'active'],
                ['name' => 'Hearing Aid Fitting', 'price' => 70000, 'status' => 'active'],
            ];
            foreach ($services as $s) { Service::create($s); }
        }

        // Seed Hearing Aids if empty
        if (HearingAid::count() === 0) {
            $hearing = [
                ['brand' => 'Unitron', 'model' => 'TMAXX600 Chargable', 'price' => 105000],
                ['brand' => 'Unitron', 'model' => 'TMAXX600 Battery', 'price' => 65000],
                ['brand' => 'Unitron', 'model' => 'StrideP500 Chargable', 'price' => 120000],
                ['brand' => 'Unitron', 'model' => 'StrideP500 Battery', 'price' => 80000],
            ];
            foreach ($hearing as $h) { HearingAid::create($h); }
        }
    }
}
