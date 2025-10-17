<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validatedUser = $request->validated();

        $user = User::create([
            "name" => $validatedUser["name"],
            "email" => $validatedUser["email"],
            "password" => $validatedUser["password"], //bcrypt($validatedUser["password"])
            "plan_id" => $validatedUser["plan_id"],
            "admin_level" => $validatedUser['admin_level']
        ]);

        return response()->json(["message" => "Admin created succefully!", "userData" => $user], Response::HTTP_CREATED);
    }
}
