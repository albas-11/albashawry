<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = 'admin@albashawri.com';

        $user = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'num' => '123456789',
                'is_active' => 1,

                //users table has role_id/ In dump Admin role id = 1.
                'role_id' => 1,
            ]
        );

        //Assign Spatie role too (creates record in model_has_roles)
        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $user->syncRoles([$adminRole]);
        }
    }
}
