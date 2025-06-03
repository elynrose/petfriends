<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create permissions table if it doesn't exist
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('title')->unique();
                $table->timestamps();
            });
        }

        // Create roles table if it doesn't exist
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('title')->unique();
                $table->timestamps();
            });
        }

        // Create permission_role pivot table if it doesn't exist
        if (!Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->id();
                $table->foreignId('permission_id')->constrained()->onDelete('cascade');
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }

        // Create role_user pivot table if it doesn't exist
        if (!Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('role_user')) {
            Schema::dropIfExists('role_user');
        }
        if (Schema::hasTable('permission_role')) {
            Schema::dropIfExists('permission_role');
        }
        if (Schema::hasTable('roles')) {
            Schema::dropIfExists('roles');
        }
        if (Schema::hasTable('permissions')) {
            Schema::dropIfExists('permissions');
        }
    }
}; 