<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';

        // Permissions from your DB dump
        $permissions = [
            'view_orders',
            'manage_products',
            'manage_reviews',
            'manage_users',
            'manage_stores',
            'manage_brands',
            'manage_categories',
            'manage_tryon',
            'manage_contacts',
            'manage_clients',
            'manage_pixels'
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm, 'guard_name' => $guard],
                []
            );
        }

        // Roles from your DB dump
        $adminRole  = Role::updateOrCreate(['name' => 'Admin',  'guard_name' => $guard], []);
        $clientRole = Role::updateOrCreate(['name' => 'Client', 'guard_name' => $guard], []);
        $userRole   = Role::updateOrCreate(['name' => 'User',   'guard_name' => $guard], []);

        // Admin gets all permissions
        $adminRole->syncPermissions(Permission::where('guard_name', $guard)->get());

        //"User" has only these 3 permissions
        $userRole->syncPermissions(
            Permission::whereIn('name', ['view_orders', 'manage_products', 'manage_reviews'])
                ->where('guard_name', $guard)
                ->get()
        );

        //Client have no permissions
        $clientRole->syncPermissions([]);
    }
}
