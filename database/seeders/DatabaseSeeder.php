<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Akhmad Nuryamin',
            'email' => 'spv1@gandariacity.com',
            'password' => bcrypt('password123'),
            'role' => 'SPV',
        ]);

        User::create([
            'name' => 'Muhammad Akmal Feruzi',
            'email' => 'spv2@gandariacity.com',
            'password' => bcrypt('password123'),
            'role' => 'SPV',
        ]);

        User::create([
            'name' => 'Rizal Maulana',
            'email' => 'cpm@gandariacity.com',
            'password' => bcrypt('password123'),
            'role' => 'CPM',
        ]);

        User::create([
            'name' => 'Irvandi Maulana',
            'email' => 'it@gandariacity.com',
            'password' => bcrypt('password123'),
            'role' => 'IT',
        ]);
    }
}
