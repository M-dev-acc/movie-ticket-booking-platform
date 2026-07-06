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
        Schema::create('theater_owners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('theater_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('assigned_by');
            $table->timestamps();

            $table->foreign('theater_id')
                ->references('id')
                ->on('theaters')
                ->onDelete('restrict');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
            $table->foreign('assigned_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
            $table->unique([
                'theater_id',
                'user_id',
                'assigned_by',
            ], 'theater_owner_assigned_by_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theater_owners');
    }
};
