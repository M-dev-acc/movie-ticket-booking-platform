<?php

namespace App\Providers;

use App\Repositories\Interfaces\MoviesRepositoryInterface;
use App\Repositories\Interfaces\TheaterRepositoryInterface;
use App\Repositories\MovieRepository;
use App\Repositories\TheaterRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TheaterRepositoryInterface::class, TheaterRepository::class);
        $this->app->bind(MoviesRepositoryInterface::class, MovieRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
