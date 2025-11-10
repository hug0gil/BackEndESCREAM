<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = $request->headers->all();
        unset($headers['authorization'], $headers['cookie']);
        /* 
        quitar datos sensibles, "authorization" contiene el token JWT 
        y "cookie" puede contener session ID, tokens CSRF, o cookies de autenticación.
        */

        $data = [
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'headers' => $headers,
            'body' => $request->except(['password']), // Nunca mostramos la contraseña
            'status' => $response->getStatusCode()
        ];

        //dd($data);
        Log::info("Request recieved:", $data);

        return $response;
    }
}
