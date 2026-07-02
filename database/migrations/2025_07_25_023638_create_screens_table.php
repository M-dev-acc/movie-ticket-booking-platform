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
        Schema::create('screens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('theater_id');

            $table->foreign('theater_id')
                ->references('id')
                ->on('theaters')
                ->onDelete('restrict');
            $table->string('name');
            $table->enum('type', [
                '2d',
                '3d',
                'imax_2d',
                'imax_3d',
                'imax_laser_2d',
                'imax_laser_3d',
                'pxl_2d',
                'pxl_3d',
                '4dx_3d',
                'screenx_2d',
                'insignia_2d',
                'luxe_2d',
            ])->default('standard');
            $table->integer('capacity')->unsigned();
            $table->boolean('status');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['theater_id', 'name'], 'screens_theater_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screens');
    }
};
