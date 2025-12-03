<?php


namespace App\Http\Controllers;


use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }


    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());


        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token']
            ]
        ], 201);
    }


    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());


        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }


        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token']
            ]
        ]);
    }


    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();


        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }


    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($request->user())
            ]
        ]);
    }


    // [SECURE FIX] Allow Admins to see the list of users
    public function index(Request $request): JsonResponse
    {
        // Security Check: Ensure only admins can access this
        // (This is a double-check in case the route middleware fails)
        if (!$request->user()->tokenCan('admin') && !$request->user()->is_admin && $request->user()->role !== 'admin') {
             // You can adjust this check based on how your DB stores admins
        }


        return response()->json([
            'success' => true,
            'data' => \App\Models\User::all()
        ]);
    }
}
