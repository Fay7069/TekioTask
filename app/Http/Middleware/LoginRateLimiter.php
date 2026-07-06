<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimiter
{
    const MAX_ATTEMPTS    = 5;
    const LOCKOUT_SECONDS = 900; // 15 minutes

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isMethod('POST') || !$request->routeIs('login.post')) {
            return $next($request);
        }

        $key      = 'login_attempts_' . $request->ip();
        $attempts = Cache::get($key, 0);

        // Locked out — reject immediately
        if ($attempts >= self::MAX_ATTEMPTS) {
            $minutesLeft = ceil(Cache::getTimeToLive($key) / 60);

            return back()->withErrors([
                'email' => "Too many failed login attempts. Please try again in {$minutesLeft} minute(s).",
            ])->withInput($request->only('email', 'role'));
        }

        $response = $next($request);

        // If login failed, increment attempt counter
        if ($response->isRedirect() && session()->has('errors')) {
            $newAttempts = $attempts + 1;
            Cache::put($key, $newAttempts, self::LOCKOUT_SECONDS);

            // Show warning when close to lockout
            $remaining = self::MAX_ATTEMPTS - $newAttempts;
            if ($remaining > 0 && $remaining <= 2) {
                session()->flash('warning', "Warning: {$remaining} attempt(s) remaining before lockout.");
            }
        } else {
            // Successful login — clear the counter
            Cache::forget($key);
        }

        return $response;
    }
}
