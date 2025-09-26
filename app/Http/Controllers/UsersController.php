<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogInRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

use function Laravel\Prompts\password;

class UsersController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->query("per_page", 10);
        $users = User::paginate($perPage);

        return response()->json($users);
    }

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

    public function register(RegisterRequest $request)
    {

        $validatedUser = $request->validated();

        $user = User::create([
            "name" => $validatedUser["name"],
            "email" => $validatedUser["email"],
            "password" => $validatedUser["password"], //bcrypt($validatedUser["password"])
        ]);

        return response()->json(["message" => "User created succefully!", $user], Response::HTTP_CREATED);
    }

    public function update(Request $request) {}

    public function delete(Request $request) {}
}
