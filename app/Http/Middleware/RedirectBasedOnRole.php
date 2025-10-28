<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Only redirect after login, not on every request
        if ($user && $request->is('admin') && !$request->is('admin/*')) {
            if ($user->hasRole('engineer') && !$user->hasRole('super_admin')) {
                return redirect('/engineer');
            }
        }

        return $next($request);
    }
}
