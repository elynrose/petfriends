<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpamIpsTable extends Migration
{
    public function up()
    {
        Schema::create('spam_ips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
