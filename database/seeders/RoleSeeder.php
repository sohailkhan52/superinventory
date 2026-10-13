<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds Spatie roles with correct schema
     */
    public function run(): void
    {
        // Reset cached permissions/roles after seeding
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define roles with Spatie schema (name only, no slug)
        $roles = [
            [
                'name' => 'Super Admin',
                'guard_name' => 'web',
                'permissions' => [], // Super admin gets all via is_super_admin flag
            ],
            [
                'name' => 'Admin',
                'guard_name' => 'web',
                'permissions' => [
                    'View Categories', 'Create Categories', 'Edit Categories', 'Delete Categories',
                    'View Users', 'Create Users', 'Edit Users', 'Delete Users',
                    'View Companies', 'Create Companies', 'Edit Companies', 'Delete Companies',
                    'View Products', 'Create Products', 'Edit Products', 'Delete Products',
                    'View Warehouses', 'Create Warehouses', 'Edit Warehouses', 'Delete Warehouses',
                    'View Inventory', 'Manage Inventory', 'Check Inventory', 'Adjust Inventory',
                    'View Purchases', 'Create Purchases', 'Update Purchases', 'Approve Purchases', 'Cancel Purchases',
                    'View Sales', 'Create Sales', 'Update Sales', 'Approve Sales', 'Cancel Sales',
                    'View Reports', 'Export Reports',
                    'View Udhar', 'Record Udhar Payments', 'Export Udhar Reports',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            // Create or update role using Spatie schema
            $role = Role::firstOrCreate(
                ['name' => $roleData['name'], 'guard_name' => $roleData['guard_name']],
                ['name' => $roleData['name'], 'guard_name' => $roleData['guard_name']]
            );

            // Assign permissions to role (if not super admin)
            if (!empty($roleData['permissions'])) {
                $permissions = Permission::whereIn('name', $roleData['permissions'])->pluck('id')->toArray();
                $role->syncPermissions($permissions);
                $this->command->info("✓ Role '{$role->name}' created/updated with " . count($permissions) . ' permissions.');
            } else {
                $this->command->info("✓ Role '{$role->name}' created/updated (gets all permissions via is_super_admin flag).");
            }
        }

        $this->command->info("\n✅ Roles seeded successfully!");
    }
}
