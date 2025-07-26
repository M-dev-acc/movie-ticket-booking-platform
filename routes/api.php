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
        Route::get('/', fn() => response()->json([
            'status' => true,
            'message' => "I am in the zone!!!"
        ]));

        Route::group([
            'prefix' => "theater"
        ], function () {
            Route::get('/', [TheaterController::class, 'index']);
            Route::post('/create', [TheaterController::class, 'store'])->middleware('permission:create theater');
            Route::patch('/update/{id}', [TheaterController::class, 'update'])->middleware('permission:update theater');
            Route::delete('/delete/{id}', [TheaterController::class, 'destroy'])->middleware('permission:delete theater');
        });
    });

    Route::group([
        'middleware' => "role:user",
    ], function () {
        
    });

});