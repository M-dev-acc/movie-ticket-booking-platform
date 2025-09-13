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
            $table->unsignedBigInteger('theater_id');
            $table->unsignedBigInteger('screen_id');

            $table->foreign('movie_id')
                ->references('uniqueid')
                ->on('movies')
                ->onDelete('cascade');
            $table->foreign('theater_id')
                ->references('id')
                ->on('theaters');
            $table->foreign('screen_id')
                ->references('id')
                ->on('screens');
            
            $table->integer('duration')->unsigned();
            $table->timestamps();
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
