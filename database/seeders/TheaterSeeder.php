<?php

namespace Database\Seeders;

use App\Models\Theater;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TheaterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Theater::factory()->count(20)->create();
    }
}
