<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('admin', [
            \Illuminate\Auth\Middleware\Authenticate::class,
            \App\Http\Middleware\Admin::class,
        ]);

        $middleware->group('bendahara', [
            \Illuminate\Auth\Middleware\Authenticate::class,
            \App\Http\Middleware\Bendahara::class,
        ]);

        $middleware->group('wali', [
            \Illuminate\Auth\Middleware\Authenticate::class,
            \App\Http\Middleware\Wali::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
