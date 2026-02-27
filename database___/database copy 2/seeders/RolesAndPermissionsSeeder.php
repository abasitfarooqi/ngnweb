<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $accessAdmin = Permission::create(['name' => 'access admin panel']);
        $manageUsers = Permission::create(['name' => 'manage users']);
        $manageContent = Permission::create(['name' => 'manage content']);
        $viewContent = Permission::create(['name' => 'view content']);

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            $accessAdmin,
            $manageUsers,
            $manageContent,
            $viewContent
        ]);

        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo($viewContent);
    }
}
