<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ScreenController;
use App\Http\Controllers\TheaterController;
use App\Models\Screen;
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
                Route::get('/', 'index');
                Route::get('/{id}', 'show')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Read Theater');
                Route::post('/create', 'store')
                    ->middleware('permission:Create Theater');
                Route::patch('/update/{id}', 'update')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Update Theater');
                Route::delete('/delete/{id}',  'destroy')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Delete Theater');
            }
        );

        Route::controller(MovieController::class)
            ->prefix('movies')
            ->group(function () {
                Route::get('/latest', [MovieController::class, 'index']);
                Route::get('/upcoming', [MovieController::class, 'upcoming']);
                Route::get('/search', [MovieController::class, 'search']);
                Route::get('/{id}', [MovieController::class, 'show'])
                    ->where('id', '[0-9]+');
            });

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
                    ->middleware('permission:Update Screen');
                Route::patch('/delete/{id}', 'destroy')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Delete Screen');
            });
    });

    Route::group([
        'middleware' => "role:owner",
    ], function () {
        Route::get('/', [AuthenticatedSessionController::class, 'loggedUser']);

        Route::controller(MovieController::class)
            ->prefix('movies')
            ->group(function () {
                Route::get('/latest', [MovieController::class, 'index']);
                Route::get('/upcoming', [MovieController::class, 'upcoming']);
                Route::get('/search', [MovieController::class, 'search']);
                Route::get('/{id}', [MovieController::class, 'show'])
                    ->where('id', '[0-9]+');
        });

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
                    ->middleware('permission:Update Theater');
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
                    ->middleware('permission:Update Screen');
                Route::patch('/delete/{id}', 'destroy')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Delete Screen');
            });
    });

    Route::group([
        'middleware' => "role:user",
    ], function () {
        Route::controller(MovieController::class)
            ->prefix('movies')
            ->group(function () {
                Route::get('/latest', [MovieController::class, 'index']);
                Route::get('/upcoming', [MovieController::class, 'upcoming']);
                Route::get('/search', [MovieController::class, 'search']);
                Route::get('/{id}', [MovieController::class, 'show'])
                    ->where('id', '[0-9]+');
        });

        Route::controller(TheaterController::class)
            ->prefix('theater')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Read Theater');
            }
        );

        Route::controller(ScreenController::class)
            ->prefix('screen')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show')
                    ->where('id', '[0-9]+')
                    ->middleware('permission:Read Screen');
            });
    });

});
