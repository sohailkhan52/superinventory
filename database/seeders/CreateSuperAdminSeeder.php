<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class CreateSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates or updates super admin user
     */
    public function run(): void
    {
        // Create or get Super Admin role
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'web'],
            ['name' => 'Super Admin', 'guard_name' => 'web']
        );

        // Create or update super admin user
        $user = User::firstOrCreate(
            ['email' => 'sohailkhan52117@gmail.com'],
            [
                'name' => 'sohail',
                'password' => bcrypt('password'),
                'status' => 'active',
                'is_super_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Update name and flag if different (in case user already existed)
        if ($user->name !== 'sohail' || !$user->is_super_admin) {
            $user->update(['name' => 'sohail', 'is_super_admin' => true]);
        }

        // Assign super admin role
        $user->assignRole($superAdminRole);

        $this->command->info('✓ Superadmin user created/updated successfully!');
        $this->command->info("  User: {$user->name} ({$user->email})");
        $this->command->info("  Role: {$superAdminRole->name}");
        $this->command->info("  is_super_admin: true");
    }
}
