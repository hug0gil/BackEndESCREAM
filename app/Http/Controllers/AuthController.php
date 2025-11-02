<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogInRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Auth\JwtAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class AuthController extends Controller
{
    public function __construct(private JwtAuthService $authService) {}

    public function logIn(LogInRequest $request)
    {
        $validated = $request->validated();

        try {
            $token = $this->authService->login($validated, $request->ip());
            return response()->json(['token' => $token], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        try {
            $user = $this->authService->register($validated);
            return response()->json([
                "message" => "User created successfully!",
                "user" => $user
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logOut()
    {
        try {
            $this->authService->logout();
            return response()->json(['message' => 'Logout successfully!'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function refresh()
    {
        try {
            $token = $this->authService->refresh();
            return response()->json([
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl')
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function who()
    {
        $user = $this->authService->me();
        return response()->json($user);
    }

    public function changeSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid plan_id.',
                'details' => $validator->errors()
            ], 422);
        }

        try {
            $user = $this->authService->changeSubscription($validator->validated());
            return response()->json([
                'message' => 'Subscription updated successfully.',
                'user' => $user
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
