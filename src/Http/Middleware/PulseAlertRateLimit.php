<?php

namespace MohamedSamy902\PulseAlert\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MohamedSamy902\PulseAlert\Services\RateLimitMonitor;

/**
 * Package: laravel-pulse-alert
 * Middleware to track request volume per user.
 */
class PulseAlertRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        RateLimitMonitor::check($request->ip());
        return $next($request);
    }
}
