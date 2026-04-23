<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@padel.com'],
            [
                'name' => 'Admin Padel',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ]
        );
    }
}
