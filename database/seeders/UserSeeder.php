<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@eyewear.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
        ]);

        // Test Client
        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
        ]);
    }
}
