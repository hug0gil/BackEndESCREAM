<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogInRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Movie;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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

    public function getPlan(int $id)
    {
        $userPlan = User::with('plan')->find($id);
        // Si solo necesito el atributo usar find pero si necesito el objeto completo uso with *Eager Loading*

        if (!$userPlan) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($userPlan->plan, Response::HTTP_OK);
    }

    public function update(RegisterRequest $request, User $user)
    {
        try {
            $user->update($request->validated()); // A user le actualizo los campos que pasan la validación

            return response()->json(["message" => "User updated successfully!", $user], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json(["errors" => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function delete(User $user)
    {
        try {
            $user->forceDelete();
            return response()->json(["message" => "User deleted successfully!"], Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json(["error" => "Cannot delete user due to database constraints"], Response::HTTP_CONFLICT);
        }
    }

    // Tener en cuenta FK de hijos, poner DELETE CASCADE o no se borrarán
    // $table->foreignId('user_id')->constrained()->cascadeOnDelete();

}
