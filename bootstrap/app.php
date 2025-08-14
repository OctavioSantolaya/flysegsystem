<?php

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
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Manejar errores de autorización (403)
        $exceptions->render(function (Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->is('admin*') || $request->is('operator*') || $request->is('manager*')) {
                return response()->view('errors.403', [], 403);
            }
        });
        
        // Manejar errores de autenticación (401) 
        $exceptions->render(function (Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('admin*') || $request->is('operator*') || $request->is('manager*')) {
                return response()->view('errors.403', [], 403);
            }
        });
    })->create();
