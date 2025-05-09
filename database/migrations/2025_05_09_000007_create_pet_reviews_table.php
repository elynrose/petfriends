<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('pet_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('score');
            $table->longText('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
