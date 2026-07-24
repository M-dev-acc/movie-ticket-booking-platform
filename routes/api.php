<?php

use App\Http\Controllers\{
    AuthenticatedSessionController,
    BookingController,
    MovieController,
    MovieShowController,
    ScreenController,
};
use Illuminate\Support\Facades\Route;

Route::controller(MovieController::class)
    ->prefix('movies')
    ->group(function () {
        Route::get('/latest', [MovieController::class, 'index']);
        Route::get('/upcoming', [MovieController::class, 'upcoming']);
        Route::get('/search', [MovieController::class, 'search']);
        Route::get('/{id}', [MovieController::class, 'show'])
        ->whereNumber('id');
    });

Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::group([
    'middleware' => "auth:sanctum",
    'prefix' => 'v1'
], function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::get('/user', [AuthenticatedSessionController::class, 'loggedUser']);

    require __DIR__ . '/admin.php';
    require __DIR__ . '/owner.php';

    Route::group([
        'middleware' => "role:user",
        'as' => "user"
    ], function () {

        Route::controller(TheaterController::class)
            ->prefix('theater')
            ->group(
                function () {
                    Route::get('/', 'index');
                    Route::get('/{id}', 'show')
                        ->where('id', '[0-9]+')
                        ->middleware('permission:Read Theater');
                }
            );

        Route::controller(ScreenController::class)
            ->prefix('screen')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Read Screen');
            });

        Route::controller(MovieShowController::class)
            ->prefix('movie-show')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Read Movie Show');
            });

        Route::controller(BookingController::class)
            ->prefix('bookings')
            ->as('.bookings')
            ->scopeBindings()
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/confirm', 'store')
                    ->name('.confirm');
                    // ->middleware('permission:Create Booking');
                Route::get('/{booking}', 'show');
                    // ->middleware('permission:Read Booking');
                Route::delete('/{booking}/cancel', 'destroy');
                    // ->middleware('permission:Delete Booking');
            });
    });

});
