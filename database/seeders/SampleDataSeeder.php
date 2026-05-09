<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Sample Asset Findings
        DB::table('asset_findings')->insert([
            [
                'finding_code' => 'FND-2026-001',
                'finding_date' => Carbon::now()->subDays(2),
                'location' => 'Main Lobby - East Wing',
                'asset_type' => 'Elevator',
                'description' => 'Elevator #3 indicator panel flickering and buttons unresponsive.',
                'status' => 'On Progress',
                'pic_id' => 1, // Dev/Admin
                'reporter' => 'Akhmad Nuryamin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'finding_code' => 'FND-2026-002',
                'finding_date' => Carbon::now()->subDay(),
                'location' => 'Basement B1 - Section C',
                'asset_type' => 'Lighting',
                'description' => 'Multiple LED fixtures burnt out near the exit ramp.',
                'status' => 'Open',
                'pic_id' => null,
                'reporter' => 'Muhammad Akmal',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // Sample Berita Acara
        DB::table('berita_acaras')->insert([
            [
                'ba_number' => 'BA/2026/05/001',
                'ba_type' => 'Kehilangan',
                'incident_date' => Carbon::now()->subDays(5),
                'customer_name' => 'Budi Santoso',
                'license_plate' => 'B 1234 ABC',
                'chronology' => 'Customer melaporkan kehilangan karcis parkir di area P2.',
                'status' => 'Done',
                'pic_id' => 1,
                'submitted_at' => Carbon::now()->subDays(4),
                'approved_at' => Carbon::now()->subDays(3),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ba_number' => 'BA/2026/05/002',
                'ba_type' => 'Kerusakan',
                'incident_date' => Carbon::now()->subDays(1),
                'customer_name' => 'Siti Aminah',
                'license_plate' => 'B 5678 XYZ',
                'chronology' => 'Spion kanan kendaraan tersenggol pembatas saat parkir di P1.',
                'status' => 'Processed',
                'pic_id' => 2, // spv1
                'submitted_at' => Carbon::now()->subHours(5),
                'approved_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
