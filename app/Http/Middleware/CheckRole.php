<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            if ($user->admin_level == 0) {
                return response()->json(['error' => 'Forbidden: insufficient permissions'], 403);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token invalid or missing'], 401);
        }

        return $next($request); // El middleware le dice a Laravel que continúe procesando la petición y pase al siguiente middleware/controller
    }
}
