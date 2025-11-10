<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminAuthService
{

    public function registerAdmin(array $validatedData): User
    {
        $user = User::create([
            "name" => $validatedData["name"],
            "email" => $validatedData["email"],
            "password" => Hash::make($validatedData["password"]),
            "plan_id" => $validatedData["plan_id"],
            "admin_level" => $validatedData["admin_level"]
        ]);

        Log::notice("New admin registered", ['user_id' => $user->id]);

        return $user;
    }
}
