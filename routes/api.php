<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\MovieShowController;
use App\Http\Controllers\ScreenController;
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

    Route::group([
        'middleware' => "role:owner",
    ], function () {
        Route::get('/', [AuthenticatedSessionController::class, 'loggedUser']);

        Route::controller(TheaterController::class)
            ->prefix('theater')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Read Theater');
                Route::post('/create', 'store')
                    ->middleware('permission:Create Theater');
                Route::patch('/update/{id}', 'update')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Edit Theater');
            }
        );

        Route::controller(ScreenController::class)
            ->prefix('screen')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Read Screen');
                Route::post('/create', 'store')
                    ->middleware('permission:Create Screen');
                Route::patch('/update/{id}', 'update')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Edit Screen');
                Route::patch('/delete/{id}', 'destroy')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Delete Screen');
            });

        Route::controller(MovieShowController::class)
            ->prefix('movie-show')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Read Movie Show');
                Route::post('/create', 'store')
                    ->middleware('permission:Create Movie Show');
                Route::patch('/update/{id}', 'update')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Edit Movie Show');
                Route::patch('/delete/{id}', 'destroy')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Delete Movie Show');
            });
    });

    Route::group([
        'middleware' => "role:user",
    ], function () {

        Route::controller(TheaterController::class)
            ->prefix('theater')
            ->group(function () {
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
    });

});
