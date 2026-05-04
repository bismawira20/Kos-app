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
        // Bikin Admin
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        // Bikin Penghuni (opsional untuk testing)
        User::firstOrCreate(
            ['email' => 'penghuni@penghuni.com'],
            [
                'name' => 'Penghuni Tester',
                'password' => bcrypt('password'),
                'role' => 'penghuni',
            ]
        );
    }
}
