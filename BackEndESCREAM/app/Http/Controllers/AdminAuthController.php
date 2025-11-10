<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Services\Auth\AdminAuthService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthController extends Controller
{

    public function __construct(private AdminAuthService $adminAuthService) {}

    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->adminAuthService->registerAdmin($request->validated());

            return response()->json([
                "message" => "Admin created successfully!",
                "userData" => $user
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error("Failed to register admin", [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                "error" => "An error occurred while creating the admin"
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
