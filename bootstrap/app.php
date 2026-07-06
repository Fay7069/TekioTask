<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->append([
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SessionTimeout::class,
        ]);

        $middleware->alias([
            'role'      => \App\Http\Middleware\RoleMiddleware::class,
            'ratelimit' => \App\Http\Middleware\LoginRateLimiter::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'csrf_mismatch'], 419);
            }
            return redirect()->route('login')->withErrors([
                'email' => 'Your session expired. Please log in again.',
            ]);
        });
    })->create();
