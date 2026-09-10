<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 🟢 MAGTIWALA SA NGROK
        $middleware->trustProxies(at: '*');

        // 🔴 ULTIMATE BYPASS: I-exempt ang login at logout sa 419 Error
        $middleware->validateCsrfTokens(except: [
            'login',
            'logout',
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\UserLastSeen::class,
        ]);

        $middleware->alias([
            'prevent-back' => \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// 🟢 Pre-set application namespace so Laravel never searches for composer.json at runtime
(function ($app) {
    try {
        $ref = new \ReflectionProperty($app, 'namespace');
        $ref->setValue($app, 'App\\');
    } catch (\Throwable $e) {
        //
    }
})($app);

return $app;