<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Permissions
        $permissions = [
            // User Management
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            // Role Management
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            // Customer Management
            'customer-list',
            'customer-create',
            'customer-edit',
            'customer-delete',
            'customer-view-profile',

            // Subscription Management
            'subscription-list',
            'subscription-create',
            'subscription-edit',
            'subscription-delete',
            'subscription-activate',
            'subscription-suspend',
            'subscription-terminate',

            // Invoice Management
            'invoice-list',
            'invoice-create',
            'invoice-edit',
            'invoice-delete',
            'invoice-print',

            // Payment Management
            'payment-list',
            'payment-create',
            'payment-edit',
            'payment-delete',

            // Package/Plan Management
            'package-list',
            'package-create',
            'package-edit',
            'package-delete',

            // Network Management (NAS/OLT)
            'nas-list',
            'nas-create',
            'nas-edit',
            'nas-delete',
            'olt-list',
            'olt-create',
            'olt-edit',
            'olt-delete',
            'network-monitor',

            // Zone/Coverage Management
            'area-list',
            'area-create',
            'area-edit',
            'area-delete',

            // Ticket/Support
            'ticket-list',
            'ticket-create',
            'ticket-edit',
            'ticket-delete',
            'ticket-reply',

            // Reports
            'report-view',
            'report-finance',
            'report-marketing',
            'report-technical',

            // System
            'system-setting-view',
            'system-setting-edit',
            'log-view',
            'dashboard-view', // Changed from dot notation for consistency or ensure it matches assignment
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Define Roles and Assign Permissions

        // 1. Super Admin
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Admin
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());
        // Ideally admin shouldn't have some critical system permissions, but for now we give all 
        // or exclude specific ones like 'system-setting-edit' if needed. 
        // Let's keep it same as super-admin but conceptually distinct for future.

        // 3. Finance
        $finance = Role::firstOrCreate(['name' => 'finance', 'guard_name' => 'web']);
        $finance->givePermissionTo([
            'payment-list',
            'payment-create',
            'payment-edit',
            'payment-delete',
            'invoice-list',
            'invoice-create',
            'invoice-edit',
            'invoice-delete',
            'invoice-print',
            'subscription-list',
            'customer-list',
            'report-view',
            'report-finance',
        ]);

        // 4. Billing
        $billing = Role::firstOrCreate(['name' => 'billing', 'guard_name' => 'web']);
        $billing->givePermissionTo([
            'payment-list',
            'payment-create',
            'invoice-list',
            'invoice-create',
            'invoice-print',
            'subscription-list',
            'customer-list',
            'report-view',
        ]);

        // 5. Technical Support
        $techSupport = Role::firstOrCreate(['name' => 'technical-support', 'guard_name' => 'web']);
        $techSupport->givePermissionTo([
            'customer-list',
            'customer-view-profile',
            'subscription-list',
            'subscription-activate',
            'subscription-suspend',
            'nas-list',
            'olt-list',
            'network-monitor',
            'ticket-list',
            'ticket-reply',
            'report-view',
            'report-technical',
        ]);

        // 6. Customer Support
        $custSupport = Role::firstOrCreate(['name' => 'customer-support', 'guard_name' => 'web']);
        $custSupport->givePermissionTo([
            'customer-list',
            'customer-view-profile',
            'customer-create',
            'customer-edit',
            'subscription-list',
            'ticket-list',
            'ticket-create',
            'ticket-reply',
            'package-list',
        ]);

        // 7. Operator
        $operator = Role::firstOrCreate(['name' => 'operator', 'guard_name' => 'web']);
        $operator->givePermissionTo([
            'customer-list',
            'customer-view-profile',
            'subscription-list',
            'ticket-list',
            'ticket-create',
            'payment-create', // Basic payment receiving
            'dashboard-view',
        ]);

        // 8. Marketing
        $marketing = Role::firstOrCreate(['name' => 'marketing', 'guard_name' => 'web']);
        $marketing->givePermissionTo([
            'customer-list',
            'customer-view-profile',
            'package-list',
            'report-view',
            'report-marketing',
            'area-list',
        ]);

        // 9. Client (Optional, existing)
        $client = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
        // Client specific permissions usually handled via specific logic/portal, not necessarily global permissions
    }
}
