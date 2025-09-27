<?php

use App\Jobs\FetchUpcomingMoviesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new FetchUpcomingMoviesJob(config('services.language_code.english')))->everyFiveMinutes();