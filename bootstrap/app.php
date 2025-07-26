<?php

use App\Http\Helpers\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthorizationException $exception) {
            return ApiResponse::error(message: "You are not authorized to perform this action.", status: 403);
        });

        // $exceptions->render(function (ValidationException $exception) {
        //     return ApiResponse::error(message: "Validation failed", status: 422);
        // });

        // $exceptions->render(function (ModelNotFoundException $exception) {
        //     return ApiResponse::error(message: "Resource not found.", status: 404);
        // });

        // $exceptions->render(function (NotFoundHttpException $exception) {
        //     return ApiResponse::error(message: "Resource not found.", status: 404);
        // });

        // $exceptions->render(function (MethodNotAllowedException $exception) {
        //     return ApiResponse::error(message: "Method is not allowed for the requested route.", status: 405);
        // });

        // $exceptions->render(function (ModelNotFoundException $exception) {
        //     return ApiResponse::error(message: "Resource not found.", status: 404);
        // });

        // $exceptions->render(function (\Throwable $exception) {
        //     return ApiResponse::error( [
        //         $exception->getMessage(),
        //     ], "Resource not found.",  500);
        // });
    })->create();
