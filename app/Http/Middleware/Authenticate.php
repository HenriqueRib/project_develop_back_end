<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);
        $this->checkJWTTokenValidity($request);

        return $next($request);
    }

    protected function checkJWTTokenValidity(Request $request)
    {
        $token = $request->bearerToken();

        if ($token) {
            try {
                JWTAuth::parseToken()->authenticate();
            } catch (\Exception $e) {
                abort(401, 'Token JWT invalid');
            }
        } else {
            abort(401, 'Token JWT not provided');
        }
    }
    
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('home');
    }
}
