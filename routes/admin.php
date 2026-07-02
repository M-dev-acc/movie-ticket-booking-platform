<?php

use App\Http\Controllers\Admin\{
    MovieShowController,
    ScreenController,
    TheaterController
};
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            "role:admin"
        ],
        'prefix' => "admin",
        'as' => "admin",
    ],
    function () {

        Route::controller(TheaterController::class)
            ->prefix('theaters')
            ->as('theaters')
            ->group(
                function () {
                    Route::get('/', 'index');
                    Route::post('/create', 'store')
                        ->middleware('permission:Create Theater');
                    Route::get('/{theater}', 'show')
                        ->middleware('permission:Read Theater');
                    Route::patch('/{theater}/update', 'update')
                        ->middleware('permission:Edit Theater');
                    Route::delete('/{theater}/delete', 'destroy')
                        ->middleware('permission:Delete Theater');
                }
            );

        Route::controller(ScreenController::class)
            ->prefix('theaters/{theater}/screens')
            ->as('.screens')
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/create', 'store')
                    ->name('create')
                    ->middleware('permission:Create Screen');
                Route::get('/{screen}', 'show')
                    ->name('show')
                    ->middleware('permission:Read Screen')
                    ->scopeBindings();
                Route::patch('/{screen}/update', 'update')
                    ->name('update')
                    ->middleware('permission:Edit Screen')
                    ->scopeBindings();
                Route::delete('/{screen}/delete', 'destroy')
                    ->name('destroy')
                    ->middleware('permission:Delete Screen')
                    ->scopeBindings();
            });

        Route::controller(MovieShowController::class)
            ->prefix('movie-shows')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{movie_show}', 'show')
                    ->middleware('permission:Read Movie Show');
                Route::post('/create', 'store')
                    ->middleware('permission:Create Movie Show');
                Route::patch('/update/{movie_show}', 'update')
                    ->middleware('permission:Edit Movie Show');
                Route::patch('/delete/{movie_show}', 'destroy')
                    ->middleware('permission:Delete Movie Show');
            });
    }
);
