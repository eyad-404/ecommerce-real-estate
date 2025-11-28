<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnsProperty
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $property = $request->route('property');
        if ($property->user_id !== auth()->id() && auth()->user()->role !== 'admin')
        {
            abort(403, "Access denied");
        }
        return $next($request);
    }
}
