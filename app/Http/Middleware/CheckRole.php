<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class CheckRole
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Obtener el usuario del token
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token missing'], 401);
        }

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Verificar permisos de admin
        if ($user->admin_level == 0) {
            return response()->json(['error' => 'Forbidden: insufficient permissions'], 403);
        }

        // Todo correcto, continuar con la petición
        return $next($request);
    }
}
