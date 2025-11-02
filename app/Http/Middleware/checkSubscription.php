<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            Log::info('CheckSubscription: Token expired - returning JSON');
            return response()->json(['error' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            Log::info('CheckSubscription: Token invalid - returning JSON');
            return response()->json(['error' => 'Token invalid'], 401);
        } catch (JWTException $e) {
            Log::info('CheckSubscription: Token missing - returning JSON');
            return response()->json(['error' => 'Token missing'], 401);
        }

        if (!$user) {
            Log::info('CheckSubscription: User not found - returning JSON');
            return response()->json(['error' => 'User not found'], 404);
        }

        //Log::info("UserData", [$user]);


        if (
            !$user->subscribed ||
            !$user->end_date ||
            now()->greaterThan($user->end_date)
        ) {
            Log::warning('User attempted to access with expired or missing subscription', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return response()->json([
                'message' => 'Your subscription has expired or is inactive.'
            ], 402);
        }

        Log::info('CheckSubscription: Passed, continuing request', ["days_remaining" => $user->daysRemaining(), "start_date" => $user->start_date, "end_date" => $user->end_date]);
        return $next($request);
    }
}
