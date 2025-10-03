<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogInRequest;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function logIn(LogInRequest $request)
    {
        $validatedData = $request->validated();

        $credentials = ['email' => $validatedData["email"], 'password' => $validatedData["password"]];

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Email or password incorrect'], Response::HTTP_UNAUTHORIZED);
            }
            return response()->json(['token' => $token], Response::HTTP_OK);
        } catch (JWTException) {
            return response()->json(['error' => 'Token generation failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
