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

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
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
        //
    })->create();
