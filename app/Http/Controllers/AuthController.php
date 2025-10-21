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
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function logIn(LogInRequest $request)
    {
        $validatedData = $request->validated();

        $credentials = ['email' => $validatedData["email"], 'password' => $validatedData["password"]];

        $userExist = User::where('email', ['email' => $validatedData["email"]])->firstOrFail();

        if (!$userExist)
            return response()->json(['error' => 'User doesn´t exist'], Response::HTTP_UNAUTHORIZED);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Email or password incorrect'], Response::HTTP_UNAUTHORIZED);
            }
            return response()->json(['token' => $token], Response::HTTP_OK);
        } catch (JWTException) {
            return response()->json(['error' => 'Token generation failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function register(RegisterRequest $request)
    {
        $validatedUser = $request->validated();

        $user = User::create([
            "name" => $validatedUser["name"],
            "email" => $validatedUser["email"],
            "password" => $validatedUser["password"], //bcrypt($validatedUser["password"])
            "plan_id" => $validatedUser["plan_id"]
        ]);

        return response()->json(["message" => "User created succefully!", "userData" => $user], Response::HTTP_CREATED);
    }

    public function logOut()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
            return response()->json(['message' => 'Logout successfully!'], Response::HTTP_OK);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unable to log out, the token is invalid'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
            $newToken = JWTAuth::refresh($token);
            return $this->respondWithToken($newToken);
        } catch (JWTException $e) {
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
