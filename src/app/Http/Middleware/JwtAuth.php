<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class JwtAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('jwt_token')) {
            return redirect('/login');
        }
        return $next($request);
    }
}
