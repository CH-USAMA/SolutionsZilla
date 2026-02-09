<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToClinic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Ensure user has a clinic
        if (!auth()->user()->clinic_id) {
            abort(403, 'User does not belong to any clinic.');
        }

        // Ensure clinic is active
        if (!auth()->user()->clinic->is_active) {
            abort(403, 'Your clinic account is inactive. Please contact support.');
        }

        return $next($request);
    }
}
