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
        'prefix' => "admin"
    ],
    function () {

        Route::controller(TheaterController::class)
            ->prefix('theaters')
            ->group(
                function () {
                    Route::get('/', 'index');
                    Route::get('/{theater}', 'show')
                        ->middleware('permission:Read Theater');
                    Route::post('/create', 'store')
                        ->middleware('permission:Create Theater');
                    Route::patch('/update/{theater}', 'update')
                        ->middleware('permission:Edit Theater');
                    Route::delete('/delete/{theater}', 'destroy')
                        ->middleware('permission:Delete Theater');
                }
            );

        Route::controller(ScreenController::class)
            ->prefix('screens')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{screen}', 'show')
                    ->middleware('permission:Read Screen');
                Route::post('/create', 'store')
                    ->middleware('permission:Create Screen');
                Route::patch('/update/{screen}', 'update')
                    ->middleware('permission:Edit Screen');
                Route::patch('/delete/{screen}', 'destroy')
                    ->middleware('permission:Delete Screen');
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
