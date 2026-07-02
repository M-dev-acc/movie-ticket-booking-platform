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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('external_id')->unique();
            $table->string('title');
            $table->string('poster_path')->nullable(true);
            $table->date('release_date');
            $table->json('genres');
            $table->decimal('rating', 3, 1)->nullable(true)->default(0.0);
            $table->string('original_language');
            $table->text('overview');

            $table->timestamps();
            $table->fullText('title', 'movie_title_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
