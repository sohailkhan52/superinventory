<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['super_admin', 'admin', 'staff'])
                      ->default('admin')
                      ->after('email');
            }

            if (!Schema::hasColumn('users', 'shop_id')) {
                $table->unsignedBigInteger('shop_id')
                      ->nullable()
                      ->after('role');

                $table->foreign('shop_id')
                      ->references('id')
                      ->on('shops')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'shop_id')) {
                $table->dropForeign(['shop_id']);
                $table->dropColumn('shop_id');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
