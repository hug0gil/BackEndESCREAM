<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;

class JwtAuthService
{
    public function login(array $credentials, string $ip)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user) {
            Log::error("User doesn't exist", ['email' => $credentials["email"], 'ip' => $ip]);
            throw new Exception("User doesn't exist");
        }

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                Log::error("Email or password incorrect", [
                    'user_id' => $user->id,
                    'email' => $credentials["email"],
                    'ip' => $ip
                ]);
                throw new Exception('Email or password incorrect');
            }

            return $token;
        } catch (JWTException $e) {
            Log::error("Token generation failed", [
                'user_id' => $user->id,
                'email' => $credentials["email"],
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            throw new Exception('Token generation failed');
        }
    }

    public function register(array $data)
    {
        try {
            $user = User::create([
                "name" => $data["name"],
                "email" => $data["email"],
                "password" => bcrypt($data["password"]),
                "plan_id" => $data["plan_id"]
            ]);
            return $user;
        } catch (Exception $e) {
            Log::error("Failed to register user", [
                'email' => $data['email'],
                'error' => $e->getMessage()
            ]);
            throw new Exception('User registration failed');
        }
    }

    public function logout()
    {
        try {
            $token = JWTAuth::getToken();
            if (! $token) throw new Exception('Token not provided');
            JWTAuth::invalidate($token);
        } catch (JWTException $e) {
            Log::error("Failed to logout user", ['error' => $e->getMessage()]);
            throw new Exception('Unable to log out, the token may be invalid or expired');
        }
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
            if (! $token) throw new Exception('Token not provided');
            return JWTAuth::refresh($token);
        } catch (JWTException $e) {
            Log::error("Failed to refresh token", ['error' => $e->getMessage()]);
            throw new Exception('Unable to refresh token');
        }
    }

    public function me()
    {
        return JWTAuth::user();
    }

    public function changeSubscription(array $validated)
    {
        try {
            $user = JWTAuth::user();
            $user->update($validated);
            return $user;
        } catch (Exception $e) {
            Log::error("Failed to update subscription", [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage()
            ]);
            throw new Exception('Failed to update subscription');
        }
    }
}
