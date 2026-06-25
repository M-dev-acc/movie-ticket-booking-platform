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
        Schema::create('movie_shows', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('movie_id');
            $table->unsignedBigInteger('screen_id');

            $table->foreign('movie_id')
                ->references('id')
                ->on('movies')
                ->onDelete('cascade');
            $table->foreign('theater_id')
                ->references('id')
                ->on('theaters');
            $table->foreign('screen_id')
                ->references('id')
                ->on('screens');

            $table->integer('duration')->unsigned();
            $table->decimal('price', 8, 2);
            $table->datetime('scheduled_at');
            /**
             * DATE_ADD(start_at, INTERVAL duration MINUTE)
             * The column is stored, meaning MySQL physically saves the calculated value in the table rather than calculating it every time you query it.
             */
            $table->datetime('end_at')->storedAs('DATE_AT(schedules_at, INTERVAL duration MINUTE)');
            $table->timestamps();

            $table->unique([
                'screen_id',
                'scheduled_at',
            ], 'movie_shows_screen_timeslot_unique');
            $table->index('scheduled_at');
            $table->index(['scheduled_at', 'schedules_at'], 'show_movie_schedule_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_shows');
    }
};
