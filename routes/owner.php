<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\Owner\{
    ScreenController, SeatController, TheaterController,
};
use Illuminate\Support\Facades\Route;

Route::group([
        'middleware' => "role:owner",
        'prefix' => "owner",
        'as' => "owner"
    ], function () {
        Route::get('/', [AuthenticatedSessionController::class, 'loggedUser']);

        Route::controller(TheaterController::class)
            ->prefix('theaters')
            ->as('.theaters')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{theater}', 'show')
                    ->name('.show')
                    ->middleware('permission:Read Theater');

                Route::patch('/{theater}/update', 'update')
                    ->name('.update')
                    ->middleware('permission:Edit Theater');
            }
        );

        Route::controller(ScreenController::class)
            ->prefix('theaters/{theater}/screens')
            ->as('.screens')
            ->scopeBindings()
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/create', 'store')
                    ->name('.create')
                    ->middleware('permission:Create Screen');
                Route::get('/{screen}', 'show')
                    ->name('.show')
                    ->middleware('permission:Read Screen');
                Route::patch('/{screen}/update', 'update')
                    ->name('.update')
                    ->middleware('permission:Edit Screen');
                Route::delete('/{screen}/delete', 'destroy')
                    ->name('.destroy')
                    ->middleware('permission:Delete Screen');
            });

        Route::controller(SeatController::class)
            ->prefix('theaters/{theater}/screens/{screen}/seats')
            ->as('.seats')
            ->scopeBindings()
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/create', 'store')
                    ->name('.create')
                    ->middleware('permission:Create Screen');
                Route::get('/{seat}', 'show')
                    ->name('.show')
                    ->middleware('permission:Read Screen');
                Route::patch('/{seat}/update', 'update')
                    ->name('.update')
                    ->middleware('permission:Edit Screen');
                Route::delete('/{seat}/delete', 'destroy')
                    ->name('.destroy')
                    ->middleware('permission:Delete Screen');
            });
    });
