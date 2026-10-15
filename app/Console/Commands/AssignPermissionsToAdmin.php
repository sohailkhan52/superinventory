<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignPermissionsToAdmin extends Command
{
    protected $signature = 'permissions:assign-to-admin';
    protected $description = 'Assign all permissions to the Admin role';

    public function handle()
    {
        $this->info('Assigning permissions to Admin role...');
        
        $adminRole = Role::where('name', 'Admin')->first();
        
        if (!$adminRole) {
            $this->error('Admin role not found!');
            return 1;
        }
        
        $permissions = Permission::all();
        
        $this->info("Found {$permissions->count()} permissions");
        
        // Assign all permissions to Admin role
        $adminRole->syncPermissions($permissions);
        
        $this->info('✅ All permissions assigned to Admin role successfully!');
        
        // Also assign to Super Admin role if it exists
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        
        if ($superAdminRole) {
            $superAdminRole->syncPermissions($permissions);
            $this->info('✅ All permissions assigned to Super Admin role successfully!');
        }
        
        return 0;
    }
}
