<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // 1. Clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define Special CRUDs (Admin & Super Admin only)
        // $specialCruds = [
        //     'user', 'role', 'permission', 'ip-restriction', 'access-log'
        // ];
        $specialCruds = [
            'user',
        ];

        // 3. Define Regular CRUDs
        // $regularCruds = [
        //     'finance-application','application-item','customer','motorbikes','pcn-case','pcn-case-exp',
        //     'vehicle-database','pcn-case-update','booking-invoice','motorbikes-sale','contract-extra-item',
        //     'customer-appointments','employee-schedule','motorbike-annual-compliance','branch','mot-booking',
        //     'calander','mot-checker','company-vehicle','motorbike-annual-compliance-m','agreement-access',
        //     'vehicle-notification','motorbike-cat-b','motorbike-repair','motorbike-repair-update','claim-motorbike',
        //     'motorbike-list','renting-pricing','motorbike','purchase-request','purchase-request-item',
        //     'upload-document-access','oxford-products','create-stock-logs','recovered-motorbike','vehicle-issuance',
        //     'customer-document','contract-access','used-vehicle-seller','ngn-product','ngn-category','ngn-model',
        //     'ngn-brand','ngn-career','ngn-stock-movement','ngn-inventory-management','ngn-product-management',
        //     'ngn-stock-handler','new-motorbike','club-member','club-member-purchase','club-member-redeem',
        //     'motorbike-record-view','ngn-renting-booking','rental-terminate-access','ngn-partner','blog-post',
        //     'blog-category','blog-tag','new-motorbikes-for-sale','vehicle-delivery-order','ec-order',
        //     'renting-service-video','motorbike-available','ngn-digital-invoice','ngn-digital-invoice-item',
        //     'pcn-tol-request','survey','survey-question','survey-option','survey-response','survey-answer',
        //     'contact-query','motorbike-delivery-order-enquiries'
        // ];
        $regularCruds = [
            'finance-application',
        ];

        // 4. Define Menu-level Permissions
        $menuPermissions = [
            'see-menu-dashboard',
            'see-menu-ecommerce',
            'see-menu-finance',
            'see-menu-rentals',
            'see-menu-pcns',
            'see-menu-services-and-repairs-and-report',
            'see-menu-mot-bookings',
            'see-menu-commons',
            'see-menu-b2b',
            'see-menu-inventory',
            'see-menu-vehicles',
            'see-menu-claims',
            'see-menu-security',
            'see-menu-permissions',
        ];

        // 5. Create CRUD Permissions
        foreach (array_merge($specialCruds, $regularCruds) as $crud) {
            Permission::firstOrCreate(['name' => "see-{$crud}", 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => "modify-{$crud}", 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => "delete-{$crud}", 'guard_name' => 'web']);
        }

        // 6. Create Menu Permissions
        foreach ($menuPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 7. Create Roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $officer = Role::firstOrCreate(['name' => 'Officer', 'guard_name' => 'web']);
        $auditor = Role::firstOrCreate(['name' => 'Auditor', 'guard_name' => 'web']);

        // 8. Assign Permissions
        // Super Admin: everything
        $superAdmin->syncPermissions(Permission::all());

        // Admin: everything
        $admin->syncPermissions(Permission::all());

        // Manager: full on regular CRUDs + menus
        foreach ($regularCruds as $crud) {
            $manager->givePermissionTo(["see-{$crud}", "modify-{$crud}", "delete-{$crud}"]);
        }
        $manager->givePermissionTo($menuPermissions);

        // Officer: see + modify only on regular CRUDs + menus
        foreach ($regularCruds as $crud) {
            $officer->givePermissionTo(["see-{$crud}", "modify-{$crud}"]);
        }
        $officer->givePermissionTo($menuPermissions);

        // Auditor: only see on regular CRUDs + menus
        foreach ($regularCruds as $crud) {
            $auditor->givePermissionTo(["see-{$crud}"]);
        }

        $auditor->givePermissionTo($menuPermissions);

        // 9. Assign Roles to Users
        $usersWithRoles = [
            93 => 'Admin',
            66 => 'Admin',
            65 => 'Admin',
            125 => 'Admin',

            109 => 'Manager',
            119 => 'Manager',
            100 => 'Manager',
            101 => 'Manager',
            102 => 'Manager',
            103 => 'Manager',
            113 => 'Manager',
            121 => 'Manager',
            123 => 'Manager',
            126 => 'Manager',
            124 => 'Manager',
            122 => 'Manager',
        ];

        foreach ($usersWithRoles as $userId => $roleName) {
            $user = User::find($userId);
            if ($user) {
                $user->syncRoles($roleName);
            }
        }

        $this->command->info('Roles, permissions, menu permissions, and user-role assignments seeded successfully.');
    }
}
