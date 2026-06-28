<?php

use App\Http\Controllers\Admin\MovieShowController;
use App\Http\Controllers\Admin\ScreenController;
use App\Http\Controllers\Admin\TheaterController as AdminTheaterController;
use App\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            "auth:sanctum",
            "role:admin"
        ],
        'prefix' => "admin"
    ],
    function () {
        Route::get('/', [AuthenticatedSessionController::class, 'loggedUser']);

        Route::controller(AdminTheaterController::class)
            ->prefix('theater')
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
    }
);
