<?php

namespace App\Providers;

use App\Repositories\Contracts\MovieRepositoryInterface;
use App\Repositories\MovieRepository;
use App\Services\ExternalApi\Contracts\MovieApiInterface;
use App\Services\ExternalApi\Http\ApiAuthenticator;
use App\Services\ExternalApi\Http\ApiClient;
use App\Services\ExternalApi\TmdbApiService;
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
                apiKey: config('services.tmdb.api_key')
            );
        });

        $this->app->singleton(ApiClient::class, function ($app) {
            return new ApiClient(
                auth: $app->make(ApiAuthenticator::class),
                baseUrl: config('services.tmdb.base_url'),
            );
        });

        $this->app->bind(MovieRepositoryInterface::class, MovieRepository::class);
        $this->app->bind(MovieApiInterface::class, TmdbApiService::class);
    }
}
