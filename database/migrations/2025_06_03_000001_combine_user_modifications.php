<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // SMS and Phone fields
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'sms_notifications')) {
                $table->boolean('sms_notifications')->default(false);
            }
            
            // Credits
            if (!Schema::hasColumn('users', 'credits')) {
                $table->integer('credits')->default(3);
            }
            
            // Premium status
            if (!Schema::hasColumn('users', 'is_premium')) {
                $table->boolean('is_premium')->default(false);
            }
            
            // Coordinates
            if (!Schema::hasColumn('users', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('users', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
            
            // Referral
            if (!Schema::hasColumn('users', 'referral_token')) {
                $table->string('referral_token')->unique()->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'phone_number',
                'sms_notifications',
                'credits',
                'is_premium',
                'latitude',
                'longitude',
                'referral_token'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 