<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogInRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function logIn(LogInRequest $request)
    {
        $validatedData = $request->validated();

        $credentials = ['email' => $validatedData["email"], 'password' => $validatedData["password"]];

        $userExist = User::where('email', $validatedData["email"])->first();

        if (!$userExist) {
            Log::error("User doesn't exist", ['email' => $validatedData["email"], 'ip' => $request->ip()]);
            return response()->json(['error' => 'User doesn´t exist'], Response::HTTP_UNAUTHORIZED);
        }
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                Log::error("Email or password incorrect", [
                    'user_id' => $userExist->id,
                    'email' => $validatedData["email"],
                    'ip' => $request->ip()
                ]);
                return response()->json(['error' => 'Email or password incorrect'], Response::HTTP_UNAUTHORIZED);
            }
            return response()->json(['token' => $token], Response::HTTP_OK);
        } catch (JWTException $e) {
            Log::error("Token generation failed", [
                'user_id' => $userExist->id,
                'email' => $validatedData["email"],
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            return response()->json(['error' => 'Token generation failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    //Introducimos en los logs email y la dirección IP para ubicar el intento y saber con que correo se intento el inicio

    public function register(RegisterRequest $request)
    {
        $validatedUser = $request->validated();

        try {
            $user = User::create([
                "name" => $validatedUser["name"],
                "email" => $validatedUser["email"],
                "password" => $validatedUser["password"], //bcrypt($validatedUser["password"])
                "plan_id" => $validatedUser["plan_id"]
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to register user", [
                'email' => $validatedUser['email'],
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'User registration failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(["message" => "User created succefully!", "userData" => $user], Response::HTTP_CREATED);
    }

    /* 
    Controlamos con log y trycatch por si a la hora de hacer la creación fallara, pero aparte en RegisterRequest creamos el método 
    "failedValidation" que es un método protegido que forma parte de los FormRequests, Laravel llama automáticamente a este método 
    cuando la validación de la request falla.
    */

    public function logOut()
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                Log::error("Failed to logout user", [
                    'error' => 'Token not provided',
                    'user_id' => null,
                ]);
                return response()->json(['error' => 'Token not provided'], Response::HTTP_BAD_REQUEST);
            }

            $user = JWTAuth::user();

            JWTAuth::invalidate($token);

            return response()->json(['message' => 'Logout successfully!'], Response::HTTP_OK);
        } catch (JWTException $e) {
            Log::error("Failed to logout user", [
                'error' => $e->getMessage(),
                'user_id' => isset($user) && $user ? $user->id : null,
            ]);

            return response()->json(['error' => 'Unable to log out, the token may be invalid or expired'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                Log::error("No token provided for refresh", ['ip' => request()->ip()]);
                return response()->json(['error' => 'Token not provided'], Response::HTTP_BAD_REQUEST);
            }

            $newToken = JWTAuth::refresh($token);
            return $this->respondWithToken($newToken);
        } catch (JWTException $e) {
            Log::error("Failed to refresh token", [
                'error' => $e->getMessage(),
                // No incluimos el token completo por seguridad
                'token_present' => isset($token),
                'ip' => request()->ip(),
            ]);
            return response()->json(['error' => 'Unable to refresh token'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function respondWithToken($token) // Protected para no acceder desde fuera
    {
        return response()->json(
            [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() // alternativa: config('jwt.ttl')
            ]
        );
    }

    public function who()
    {
        $user = JWTAuth::user();
        return response()->json($user);
    }

    public function changeSubscription(Request $request)
    {
        $user = JWTAuth::user();
        $request->headers->set('Accept', 'application/json');

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer|in:1,2,3',
        ], [
            'plan_id.required' => 'The plan id is required.',
            'plan_id.integer' => 'The plan id must be an integer.',
            'plan_id.in' => 'The selected plan id is invalid. It must be 1, 2 or 3.',
        ]);

        if ($validator->fails()) {
            Log::warning("Failed subscription validation", [
                'user_id' => $user->id,
                'errors' => $validator->errors(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'error' => 'Invalid plan_id.',
                'details' => $validator->errors()
            ], 422);
        }

        try {
            $user->update($validator->validated());

            return response()->json([
                'message' => 'Subscription updated successfully.',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            Log::error("Failed to update subscription", [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to update subscription.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
