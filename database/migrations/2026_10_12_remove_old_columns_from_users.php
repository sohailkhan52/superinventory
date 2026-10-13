<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Removes columns not needed for multi-tenant architecture:
     * - warehouse_id: Old system, use roles/permissions instead
     * - remember_token: Not needed, use sessions for "remember me"
     * - role: Handled by Spatie's role_user junction table
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns_to_drop = [];

            // Remove warehouse_id (old system, not used in multi-tenant)
            if (Schema::hasColumn('users', 'warehouse_id')) {
                if (Schema::hasForeignKey('users', 'users_warehouse_id_foreign')) {
                    $table->dropForeignKeyIfExists(['warehouse_id']);
                }
                $columns_to_drop[] = 'warehouse_id';
            }

            // Remove remember_token (not needed for modern apps)
            if (Schema::hasColumn('users', 'remember_token')) {
                $columns_to_drop[] = 'remember_token';
            }

            // Remove role (handled by role_user table)
            if (Schema::hasColumn('users', 'role')) {
                $columns_to_drop[] = 'role';
            }

            if (!empty($columns_to_drop)) {
                $table->dropColumn($columns_to_drop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('warehouse_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('warehouses')
                  ->nullOnDelete();

            $table->rememberToken();

            $table->enum('role', ['super_admin', 'admin', 'staff'])
                  ->default('admin')
                  ->after('email');
        });
    }
};
