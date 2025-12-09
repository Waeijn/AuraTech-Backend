<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@auratech.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
            'email_verified_at' => now()
        ]);

        // Regular user
        User::create([
            'name' => 'Customer User',
            'email' => 'user@auratech.com',
            'password' => Hash::make('password123'),
            'is_admin' => false,
            'email_verified_at' => now()
        ]);

    }
}
