<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        $adminRole  = Role::where('name', 'Admin')->where('guard_name', $guard)->first();
        $clientRole = Role::where('name', 'Client')->where('guard_name', $guard)->first();
        $userRole   = Role::where('name', 'User')->where('guard_name', $guard)->first();

        //Admin test
        $admin = User::updateOrCreate(
            ['email' => 'admin@albashawri.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password@admin'),
                'num' => '09888876566',
                'role_id' => 1, // Admin
                'is_active' => 1,
            ]
        );
        if ($adminRole) $admin->syncRoles([$adminRole]);

        //Client test
        $client = User::updateOrCreate(
            ['email' => 'client@gmail.com'],
            [
                'name' => 'Test Client',
                'password' => Hash::make('password@client'),
                'num' => '0612345678',
                'role_id' => 2, // Client
                'is_active' => 1,
            ]
        );
        if ($clientRole) $client->syncRoles([$clientRole]);

        //Normal user test
        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password@user'),
                'num' => null,
                'role_id' => 3, // User
                'is_active' => 1,
            ]
        );
        if ($userRole) $user->syncRoles([$userRole]);

        //Extra demo users
        $moreUsers = [
            [
                'name' => 'Mohammed',
                'email' => 'mohammed@gmail.com',
                'num' => null,
                'role_id' => 2,
                'role' => $clientRole,
            ],
            [
                'name' => 'Zakaria',
                'email' => 'zakaria@gmail.com',
                'num' => '0988887748',
                'role_id' => 3,
                'role' => $userRole,
            ],
            [
                'name' => 'Sara',
                'email' => 'sara@gmail.com',
                'num' => null,
                'role_id' => 3,
                'role' => $userRole,
            ],
        ];

        foreach ($moreUsers as $u) {
            $created = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'num' => $u['num'],
                    'role_id' => $u['role_id'],
                    'is_active' => 1,
                ]
            );

            if ($u['role']) {
                $created->syncRoles([$u['role']]);
            }
        }
    }
}
