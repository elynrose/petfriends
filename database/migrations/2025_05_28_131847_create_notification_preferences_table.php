<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('pet_available')->default(true);
            $table->boolean('booking_requested')->default(true);
            $table->boolean('booking_accepted')->default(true);
            $table->boolean('booking_rejected')->default(true);
            $table->boolean('booking_completed')->default(true);
            $table->boolean('new_message')->default(true);
            $table->boolean('email_notifications')->default(true);
            $table->timestamps();
            
            // Ensure each user has only one set of preferences
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
