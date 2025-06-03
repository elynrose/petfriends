<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Only add columns/keys if they don't exist
        Schema::table('pet_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('pet_reviews', 'pet_id')) {
                $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('pet_reviews', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('pet_reviews', 'booking_id')) {
                $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('pet_reviews', 'rating')) {
                $table->integer('rating');
            }
            if (!Schema::hasColumn('pet_reviews', 'comment')) {
                $table->text('comment')->nullable();
            }
            if (!Schema::hasColumn('pet_reviews', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        Schema::table('pet_reviews', function (Blueprint $table) {
            $columns = [
                'pet_id',
                'user_id',
                'booking_id',
                'rating',
                'comment',
                'created_at',
                'updated_at'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('pet_reviews', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 