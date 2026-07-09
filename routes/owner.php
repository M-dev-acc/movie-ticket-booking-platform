<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\Owner\{
    TheaterController,
};
use Illuminate\Support\Facades\Route;

Route::group([
        'middleware' => "role:owner",
        'prefix' => "owner",
        'as' => ".owner"
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

    });
