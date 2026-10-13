<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates super admin user with is_super_admin=true flag
     */
    public function run(): void
    {
        // Create Super Admin from environment variables or use defaults
        $superAdmin = [
            'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
            'email' => env('SUPER_ADMIN_EMAIL', 'admin@example.com'),
            'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'password')),
            'phone' => env('SUPER_ADMIN_PHONE', null),
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
            'is_super_admin' => true, // Mark as super admin via flag
        ];

        // Check if user already exists
        $existingUser = User::where('email', $superAdmin['email'])->first();

        if ($existingUser) {
            $this->command->info("✓ Super Admin user already exists: {$superAdmin['email']}");
            
            // Ensure is_super_admin flag is set
            if (!$existingUser->is_super_admin) {
                $existingUser->update(['is_super_admin' => true]);
                $this->command->info("✓ Updated is_super_admin flag");
            }
            
            return;
        }

        // Create the super admin user
        $user = User::create($superAdmin);

        // Optionally assign Super Admin role for Spatie compatibility
        $superAdminRole = Role::where('name', 'Super Admin')->where('guard_name', 'web')->first();
        if ($superAdminRole) {
            $user->assignRole($superAdminRole);
            $this->command->info("✓ Super Admin role assigned");
        }

        $this->command->info("✅ Super Admin user created successfully!");
        $this->command->info("   Email: {$superAdmin['email']}");
        $this->command->info("   is_super_admin: true");
        
        if (app()->environment('local', 'development')) {
            $this->command->warn("   Password: " . env('SUPER_ADMIN_PASSWORD', 'password'));
            $this->command->warn("   ⚠️  Change this password immediately in production!");
        }
    }
}
