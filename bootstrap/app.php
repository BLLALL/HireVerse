<?php

use App\Classes\ExceptionHandler;
use App\Http\Middleware\ApiGuest;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'api_guest' => ApiGuest::class,
        ]);
        $middleware->append(HandleCors::class);
        $middleware->trustProxies(at: ['172.18.0.5']);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Throwable $exception, Request $request) {
            return (new ExceptionHandler)->render($request, $exception);
        });
    })
    ->create();
