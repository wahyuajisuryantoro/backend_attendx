<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect('/')->with('error', 'You must be logged in to access this page.');
        }

        if (!$request->user()->is_admin) {
            return redirect('/')->with('error', 'Insufficient permissions.');
        }

        return $next($request);
    }
}