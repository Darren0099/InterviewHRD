<?php

use App\Http\Middleware\HrdAuth;
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
        // Hosting berada di belakang proxy/CDN (mis. Wasmer): percaya header X-Forwarded-*
        $middleware->trustProxies(at: '*');
        $middleware->alias(['hrd.auth' => HrdAuth::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
