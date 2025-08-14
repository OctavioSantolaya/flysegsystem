<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;

class HandleFilamentAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (AuthorizationException $e) {
            // Si es una petición a un panel de Filament y hay un error de autorización
            if ($request->is('admin*') || $request->is('operator*') || $request->is('manager*')) {
                return response()->view('errors.403', [], 403);
            }
            
            throw $e;
        }
    }
}
