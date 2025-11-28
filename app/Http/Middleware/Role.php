<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        // Check if user role exists inside allowed roles
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, "Access denied");
        }

        return $next($request);
    }
}
