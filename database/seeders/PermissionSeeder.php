<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds permissions using Spatie's correct schema (name only, no slug)
     */
    public function run(): void
    {
        // Reset cached permissions after seeding
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Purchase Management (dot notation used in code)
            'purchases.view',
            'purchases.create',
            'purchases.update',
            'purchases.approve',
            'purchases.cancel',

            // Sales Management (dot notation used in code)
            'sales.view',
            'sales.create',
            'sales.update',
            'sales.cancel',
            'sales.approve',

            // Udhar Management (Credit/Outstanding) (dot notation used in code)
            'udhar.view',
            'udhar.payments',
            'udhar.reports',
            
            // Payables Management
            'payables.view',
            'payables.manage',

            // Category Management
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',

            // User Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Company Management
            'companies.view',
            'companies.create',
            'companies.edit',
            'companies.delete',

            // Product Management
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',

            // Warehouse Management
            'warehouses.view',
            'warehouses.create',
            'warehouses.edit',
            'warehouses.delete',

            // Inventory Management
            'inventory.view',
            'inventory.manage',
            'inventory.check',
            'inventory.adjust',

            // Reports
            'reports.view',
            'reports.export',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }

        $this->command->info('✅ Permissions seeded successfully! Total: ' . count($permissions));
    }
}
