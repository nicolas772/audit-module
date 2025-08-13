<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetCurrentTenant;
use App\Http\Middleware\AttachTxHash;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(SetCurrentTenant::class);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(AttachTxHash::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
