<?php

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
    }
);
