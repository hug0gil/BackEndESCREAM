<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validatedUser = $request->validated();

        try {
            $user = User::create([
                "name" => $validatedUser["name"],
                "email" => $validatedUser["email"],
                "password" => $validatedUser["password"], //bcrypt($validatedUser["password"])
                "plan_id" => $validatedUser["plan_id"],
                "admin_level" => $validatedUser['admin_level']
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to register admin", [
                'email' => $validatedUser['email'],
                'error' => $e->getMessage()
            ]);

            return response()->json(["message" => "Admin created succefully!", "userData" => $user], Response::HTTP_CREATED);
        }
    }
}
