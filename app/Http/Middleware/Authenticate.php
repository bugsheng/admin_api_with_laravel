<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Closure;
use Auth;

class Authenticate
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, $guard = null)
    {

        if (!Auth::guard($guard)->check()) {
            throw new AuthenticationException();
        }

        return $next($request);
    }

}
