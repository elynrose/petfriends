<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToChatsTable extends Migration
{
    public function up()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->foreign('booking_id', 'booking_fk_10567549')->references('id')->on('bookings');
            $table->unsignedBigInteger('from_id')->nullable();
            $table->foreign('from_id', 'from_fk_10567551')->references('id')->on('users');
        });
    }
}
