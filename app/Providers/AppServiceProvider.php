<?php

namespace App\Providers;

use App\Repositories\Interfaces\TheaterRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use TheaterRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TheaterRepositoryInterface::class, TheaterRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
