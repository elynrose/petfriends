<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'referral_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('referral_token')->unique()->nullable()->after('email');
            });

            // Generate unique referral tokens for existing users
            User::whereNull('referral_token')->each(function ($user) {
                do {
                    $token = Str::random(8); // Using 8 characters for shorter, more user-friendly tokens
                } while (User::where('referral_token', $token)->exists());
                
                $user->update(['referral_token' => $token]);
            });
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referral_token');
        });
    }
}; 