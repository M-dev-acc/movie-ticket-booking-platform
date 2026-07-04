<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\Owner\{
    TheaterController,
};
use Illuminate\Support\Facades\Route;

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

    });
