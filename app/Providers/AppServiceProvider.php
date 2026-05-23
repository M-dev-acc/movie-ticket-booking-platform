<?php

namespace App\Providers;

use ApiClient;
use App\Repositories\Interfaces\MoviesRepositoryInterface;
use App\Repositories\Interfaces\TheaterRepositoryInterface;
use App\Repositories\MovieRepository;
use App\Repositories\TheaterRepository;
use App\Services\ExternalApi\Http\ApiAuthenticator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ApiAuthenticator::class, function () {
            return new ApiAuthenticator(
                apiKey: rtrim(config('services.movies_db.base_url'), '/')
            );
        });

        $this->app->singleton(ApiClient::class, function ($app) {
            return new ApiClient(
                auth: $app->make(ApiAuthenticator::class),
                baseUrl: config('services.tmdb.base_url'),
            );
        });

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
