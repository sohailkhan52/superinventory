<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique()->nullable();
            $table->string('email')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('city')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])
                  ->default('pending');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            $table->index('status');
            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
