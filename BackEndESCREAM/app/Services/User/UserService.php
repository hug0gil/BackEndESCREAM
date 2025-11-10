<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class UserService
{

    public function getAllPaginated(int $perPage = 10)
    {
        return User::paginate($perPage);
    }

    public function getUserPlan(int $id)
    {
        // Si solo necesito el atributo usar find pero si necesito el objeto completo uso with *Eager Loading*
        return User::with('plan')->find($id);
    }

    public function updateUser(User $user, array $validatedData): User
    {
        $user->update($validatedData);
        Log::notice("User updated account", ['user_id' => $user->id]);

        return $user;
    }

    public function deleteUser(User $user): bool
    {
        try {
            $user->delete();
            Log::notice('User deleted successfully', ['user_id' => $user->id]);
            return true;
        } catch (QueryException $e) {
            Log::error('Failed to delete user', [
                'user_id' => $user->id,
                'error_message' => $e->getMessage(),
            ]);
            return false;
        }
    }
    public function getUser(User $user): ?User
    {
        if (!$user) {
            Log::warning('User not found when attempting to fetch details');
            return null;
        }

        Log::info('User fetched successfully', ['user_id' => $user->id]);
        return $user;
    }
}
