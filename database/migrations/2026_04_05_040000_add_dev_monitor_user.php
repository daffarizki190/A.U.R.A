<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek apakah akun DEV sudah ada
        $exists = DB::table('users')->where('email', 'dev@gandariacity.com')->exists();

        if (!$exists) {
            DB::table('users')->insert([
                'name'       => 'Dev Monitor',
                'email'      => 'dev@gandariacity.com',
                'password'   => Hash::make('devmonitor2026!'),
                'role'       => 'DEV',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('email', 'dev@gandariacity.com')->delete();
    }
};
