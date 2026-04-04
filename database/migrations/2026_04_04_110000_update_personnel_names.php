<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $names = [
            'Yamin' => 'Akhmad Nuryamin',
            'Akmal' => 'Muhammad Akmal Feruzi',
            'Rizal' => 'Rizal Maulana',
            'Irvandi' => 'Irvandi Maulana',
        ];

        foreach ($names as $short => $full) {
            DB::table('users')
                ->where('name', $short)
                ->update(['name' => $full]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $names = [
            'Akhmad Nuryamin' => 'Yamin',
            'Muhammad Akmal Feruzi' => 'Akmal',
            'Rizal Maulana' => 'Rizal',
            'Irvandi Maulana' => 'Irvandi',
        ];

        foreach ($names as $full => $short) {
            DB::table('users')
                ->where('name', $full)
                ->update(['name' => $short]);
        }
    }
};
