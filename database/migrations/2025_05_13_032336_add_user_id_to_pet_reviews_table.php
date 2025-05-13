<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToPetReviewsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pet_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('pet_reviews', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id', 'pet_reviews_user_id_fk')->references('id')->on('users');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pet_reviews', function (Blueprint $table) {
            $table->dropForeign('pet_reviews_user_id_fk');
            $table->dropColumn('user_id');
        });
    }
} 