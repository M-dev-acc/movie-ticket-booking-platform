<?php

use App\Http\Controllers\AuthenticatedSessionController;
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
    ], function () {
        Route::get('/admin-only', fn() => response()->json([
            'status' => true,
            'message' => "I am in the zone!!!"
        ]));
    });

    Route::group([
        'middleware' => "role:user",
    ], function () {
        
    });

});