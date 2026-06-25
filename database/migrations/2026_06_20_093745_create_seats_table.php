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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('screen_id');
            $table->foreign('screen_id')
                ->references('id')
                ->on('screens')
                ->onDelete('cascade');
            $table->char('row', 1);
            $table->unsignedSmallInteger('number');
            $table->enum('type', [
                'standard', 'premium', 'recliner'
            ]);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->unique([
                'screen_id',
                'row',
                'number',
            ], 'seats_screen_position_unique');
            $table->index('screen_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
