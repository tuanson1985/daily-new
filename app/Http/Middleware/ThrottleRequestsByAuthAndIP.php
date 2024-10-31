<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThrottleRequestsByAuthAndIP
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next, $maxAttempts = 20, $decayMinutes = 1)
    {
        $user = Auth::user();
        $ip = $request->ip();
        $key = $this->resolveRequestSignature($request, $user, $ip);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
//            return response(view('errors.429'), 429);
            return response('Too many requests', 429);
//            return response()->json(['message' => 'Too many requests'], 429);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );

        return $response;
    }

    protected function resolveRequestSignature(Request $request, $user, $ip)
    {
        if ($user) {
            return sha1($user->getAuthIdentifier() . '|' . $ip);
        }

        return sha1($ip);
    }

    protected function calculateRemainingAttempts($key, $maxAttempts)
    {
        return $this->limiter->remaining($key, $maxAttempts);
    }

    protected function addHeaders($response, $maxAttempts, $remainingAttempts)
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ]);
    }
}
