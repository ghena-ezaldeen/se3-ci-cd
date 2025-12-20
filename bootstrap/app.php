<?php

/*
|--------------------------------------------------------------------------
| Application Bootstrap
|--------------------------------------------------------------------------
| Wires the core Laravel app, routes, middleware, and exception handling.
| Account routes need to be testable from Postman without CSRF tokens, so
| we exempt those URIs from CSRF validation here without moving routes or
| altering other modules’ middleware stacks.
*/

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'accounts',
            'accounts/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // AuthorizationException (403)
        $exceptions->render(function (AuthorizationException $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'You are not authorized to perform this action',
                'status'  => 403,
            ], 403);
        });

        // AccessDeniedHttpException (403)
        $exceptions->render(function (AccessDeniedHttpException $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'You are not authorized to perform this action',
                'status'  => 403,
            ], 403);
        });

        // Route Model Binding - Model Not Found (404)
        $exceptions->render(function (NotFoundHttpException $e) {
            $previous = $e->getPrevious();

            if ($previous instanceof ModelNotFoundException) {
                $model = class_basename($previous->getModel());

                return response()->json([
                    'message' => "The requested {$model} was not found",
                    'status'  => 404,
                ], 404);
            }
            return null;
        });
    })->create();
