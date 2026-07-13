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
        Schema::create('show_seats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('show_id');
            $table->unsignedBigInteger('seat_id');
            $table->enum('status', [
                'available',
                'locked',
                'booked',
            ])->default('available');
            $table->decimal('price', 8, 2);
            $table->timestamp('locked_until');
            $table->timestamps();

            $table->foreign('show_id')
                ->references('id')
                ->on('shows')
                ->onDelete('restrict');
            $table->foreign('seat_id')
                ->references('id')
                ->on('seats')
                ->onDelete('restrict');
            $table->unique(
                ['show_id', 'seat_id'],
                'show_seats_availabilty_unique'
            );
            $table->index('status', 'show_seats_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('show_seats');
    }
};
