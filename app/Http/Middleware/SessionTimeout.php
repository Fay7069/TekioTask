<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    const TIMEOUT_MINUTES = 120;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && $request->hasSession()) {
            $lastActivity = $request->session()->get('last_activity');

            if ($lastActivity && (time() - $lastActivity) > (self::TIMEOUT_MINUTES * 60)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // If it's a fetch/AJAX request, return JSON instead of redirect
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'session_expired'], 401);
                }

                return redirect()->route('login')->withErrors([
                    'email' => 'Your session expired. Please log in again.',
                ]);
            }

            $request->session()->put('last_activity', time());
        }

        return $next($request);
    }
}
