<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Assuming your User model has a relationship 'role' and Role model has a 'name' attribute
        if (!auth()->check() || !auth()->user()->role || (auth()->user()->role->name !== 'Admin' && auth()->user()->role->name !== 'Super Admin') ) {
            // You can redirect to a specific page or abort
            // abort(403, 'Unauthorized action.');
            return redirect('/')->with('error', 'You do not have administrative access.');
        }
        return $next($request);
    }
}