<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate data from custom permissions table to Spatie permissions table
        if (Schema::hasTable('permissions')) {
            $customPermissions = DB::table('permissions')->get();
            
            foreach ($customPermissions as $permission) {
                DB::table('permissions')->updateOrInsert(
                    ['id' => $permission->id],
                    [
                        'name' => $permission->name,
                        'guard_name' => 'web',
                        'created_at' => $permission->created_at ?? now(),
                        'updated_at' => $permission->updated_at ?? now(),
                    ]
                );
            }
        }

        // Migrate data from custom roles table to Spatie roles table
        if (Schema::hasTable('roles')) {
            $customRoles = DB::table('roles')->get();
            
            foreach ($customRoles as $role) {
                DB::table('roles')->updateOrInsert(
                    ['id' => $role->id],
                    [
                        'name' => $role->name,
                        'guard_name' => 'web',
                        'created_at' => $role->created_at ?? now(),
                        'updated_at' => $role->updated_at ?? now(),
                    ]
                );
            }
        }

        // Migrate role-permission relationships
        if (Schema::hasTable('role_permission')) {
            $rolePermissions = DB::table('role_permission')->get();
            
            foreach ($rolePermissions as $rp) {
                DB::table('role_has_permissions')->updateOrInsert(
                    ['role_id' => $rp->role_id, 'permission_id' => $rp->permission_id],
                    ['permission_id' => $rp->permission_id]
                );
            }
        }

        // Migrate role-user relationships
        if (Schema::hasTable('role_user')) {
            $roleUsers = DB::table('role_user')->get();
            
            foreach ($roleUsers as $ru) {
                DB::table('model_has_roles')->updateOrInsert(
                    ['role_id' => $ru->role_id, 'model_id' => $ru->user_id],
                    [
                        'model_type' => 'App\Models\User',
                        'model_id' => $ru->user_id,
                        'role_id' => $ru->role_id,
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear the Spatie tables
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
    }
};
