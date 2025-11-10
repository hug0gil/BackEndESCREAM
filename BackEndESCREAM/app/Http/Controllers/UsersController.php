<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{

    public function __construct(private UserService $userService) {}

    public function index(Request $request)
    {
        $perPage = $request->query("per_page", 10);
        $users = $this->userService->getAllPaginated($perPage);

        return response()->json($users, Response::HTTP_OK);
    }

    public function getPlan(int $id)
    {
        $user = $this->userService->getUserPlan($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($user->plan, Response::HTTP_OK);
    }

    public function update(RegisterRequest $request, User $user)
    {
        try {
            $updatedUser = $this->userService->updateUser($user, $request->validated());

            return response()->json([
                "message" => "User updated successfully!",
                "user" => $updatedUser
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                "errors" => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Failed to update user",
                "details" => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(User $user)
    {
        $deleted = $this->userService->deleteUser($user);

        if ($deleted) {
            return response()->json(["message" => "User deleted successfully!"], Response::HTTP_OK);
        }

        return response()->json(["error" => "Cannot delete user due to database constraints"], Response::HTTP_CONFLICT);
    }

    public function show(User $user)
    {
        $userData = $this->userService->getUser($user);

        if (!$userData) {
            return response()->json(["error" => "User not found!"], Response::HTTP_NOT_FOUND);
        }

        return response()->json($userData, Response::HTTP_OK);
    }


    // Tener en cuenta FK de hijos, poner DELETE CASCADE o no se borrarán
    // $table->foreignId('user_id')->constrained()->cascadeOnDelete();

}
