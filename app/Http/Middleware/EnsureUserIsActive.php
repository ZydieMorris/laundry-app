<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->isActive()) {
            return $next($request);
        }

        $routeName = (string) $request->route()?->getName();

        $isDashboard = $routeName === 'dashboard' || str_ends_with($routeName, '.dashboard');
        $isVerification = $routeName === 'verification.notice' || $routeName === 'verification.verify';
        $isLogout = $routeName === 'logout';

        if (($isDashboard && $request->isMethod('GET')) || $isVerification || $isLogout) {
            return $next($request);
        }

        $status = $request->isMethod('GET') ? 302 : 303;

        return redirect()->route('dashboard', status: $status);
    }
}
