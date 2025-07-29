<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::create([
            'name' => 'System Admin',
            'first_name' => 'System',
            'last_name' => 'Admin',
            'email' => 'admin@disasterlink.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create sample responder
        User::create([
            'name' => 'John Responder',
            'first_name' => 'John',
            'last_name' => 'Responder',
            'email' => 'responder@disasterlink.com',
            'password' => Hash::make('responder123'),
            'role' => 'responder',
            'email_verified_at' => now(),
        ]);

        // Create sample civilian
        User::create([
            'name' => 'Jane Civilian',
            'first_name' => 'Jane',
            'last_name' => 'Civilian',
            'email' => 'civilian@disasterlink.com',
            'password' => Hash::make('civilian123'),
            'role' => 'civilian',
            'email_verified_at' => now(),
        ]);
    }
}
