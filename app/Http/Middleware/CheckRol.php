<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
       if (!$request->user()->hasRole($role)) {
            abort(403, 'No tienes autorización para acceder a esta página');
        }
        return $next($request);
    }
}
