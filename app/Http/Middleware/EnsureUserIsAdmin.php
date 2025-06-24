<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
 public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) { // هذا سيعمل بشكل صحيح إذا قام Sanctum بالمصادقة
            abort(401, 'Unauthenticated.');
        }

        if (Auth::user() && Auth::user()->role && strtolower(Auth::user()->role->name) === 'admin') {
            return $next($request);
        }
        abort(403, 'Unauthorized action.');
    }
}