<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated as admin (User model)
        if (!Auth::guard('web')->check()) {
            return redirect()->route('admin.login');
        }

        // If employee is logged in, redirect to employee dashboard
        if (Auth::guard('employee')->check()) {
            return redirect()->route('employee.dashboard');
        }

        return $next($request);
    }
}
