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
        Schema::create('reservations', function (Blueprint $table) {
            // RESERVATIONS
            $table->id(); // reservation_id
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->integer('approved_by')->nullable();
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('purpose');
            $table->integer('attendees_count');
            // status
            $table->enum('reservation_status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
