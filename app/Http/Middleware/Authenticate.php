<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // return $request->expectsJson() ? null : route('login');
        return $this->noAutorizado($request);
    }
    protected function noAutorizado(Request $request)
    {

        if ($request->expectsJson()) {
            return response()->json(['message' => 'No autorizado'], 401);
        }
    }
}
