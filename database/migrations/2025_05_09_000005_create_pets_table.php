<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetsTable extends Migration
{
    public function up()
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->string('name');
            $table->integer('age');
            $table->string('gender');
            $table->string('notes')->nullable();
            $table->boolean('not_available')->default(0)->nullable();
            $table->date('from')->nullable();
            $table->time('from_time')->nullable();
            $table->date('to')->nullable();
            $table->time('to_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
