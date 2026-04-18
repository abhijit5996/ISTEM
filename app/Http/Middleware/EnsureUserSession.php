<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('web_user_id')) {
            return redirect()->route('web.login')->with('error', 'Please login first.');
        }

        return $next($request);
    }
}
