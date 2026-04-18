<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('web_admin_id')) {
            return redirect()->route('web.admin.login')->with('error', 'Please login as admin.');
        }

        return $next($request);
    }
}
