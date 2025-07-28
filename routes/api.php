<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\TheaterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Authentication routes
 */
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');

Route::group([
    'middleware' => "auth:sanctum",
    'prefix' => 'v1'
], function () {

    Route::group([
        'middleware' => "role:admin",
        'prefix' => "admin"
    ], function () {
        Route::get('/', [AuthenticatedSessionController::class, 'loggedUser']);

        Route::controller(TheaterController::class)
            ->prefix('theater')
            ->group(function () {
                Route::get('/page/{page}', 'index')
                    ->where('page', '[0-9]+');
                Route::get('/{id}', 'show')
                    ->where('id', '[0-9]+');
                Route::post('/create', 'store')
                    ->middleware('permission:create theater');
                Route::patch('/update/{id}', 'update')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:update theater');
                Route::delete('/delete/{id}',  'destroy')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:delete theater');
            }
        );

    });

    Route::group([
        'middleware' => "role:user",
    ], function () {
        
    });

});