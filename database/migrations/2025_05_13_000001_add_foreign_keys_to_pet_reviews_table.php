<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPetReviewsTable extends Migration
{
    public function up()
    {
        Schema::table('pet_reviews', function (Blueprint $table) {
            // Only add foreign keys, do not add columns
            if (Schema::hasColumn('pet_reviews', 'pet_id')) {
                $table->foreign('pet_id', 'pet_reviews_pet_id_fk')->references('id')->on('pets');
            }
            if (Schema::hasColumn('pet_reviews', 'booking_id')) {
                $table->foreign('booking_id', 'pet_reviews_booking_id_fk')->references('id')->on('bookings');
            }
            if (Schema::hasColumn('pet_reviews', 'user_id')) {
                $table->foreign('user_id', 'pet_reviews_user_id_fk')->references('id')->on('users');
            }
        });
    }

    public function down()
    {
        Schema::table('pet_reviews', function (Blueprint $table) {
            $table->dropForeign('pet_reviews_pet_id_fk');
            $table->dropForeign('pet_reviews_booking_id_fk');
            $table->dropForeign('pet_reviews_user_id_fk');
        });
    }
} 