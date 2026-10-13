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
            // Category Management
            'View Categories',
            'Create Categories',
            'Edit Categories',
            'Delete Categories',

            // User Management
            'View Users',
            'Create Users',
            'Edit Users',
            'Delete Users',

            // Company Management
            'View Companies',
            'Create Companies',
            'Edit Companies',
            'Delete Companies',

            // Product Management
            'View Products',
            'Create Products',
            'Edit Products',
            'Delete Products',

            // Warehouse Management
            'View Warehouses',
            'Create Warehouses',
            'Edit Warehouses',
            'Delete Warehouses',

            // Inventory Management
            'View Inventory',
            'Manage Inventory',
            'Check Inventory',
            'Adjust Inventory',

            // Purchase Management
            'View Purchases',
            'Create Purchases',
            'Update Purchases',
            'Approve Purchases',
            'Cancel Purchases',

            // Sales Management
            'View Sales',
            'Create Sales',
            'Update Sales',
            'Cancel Sales',
            'Approve Sales',

            // Reports
            'View Reports',
            'Export Reports',

            // Udhar Management (Credit/Outstanding)
            'View Udhar',
            'Record Udhar Payments',
            'Export Udhar Reports',
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
