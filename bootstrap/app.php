<?php

use App\Http\Middleware\IsAuthenticate;
use App\Http\Middleware\UserLoginAuthenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'userAuth' => UserLoginAuthenticate::class,
            'isAuthenticate' => IsAuthenticate::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
