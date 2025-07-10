<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\AuthenticationService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthenticationController extends Controller
{
    public function __construct(protected AuthenticationService $authService)
    {
    }

    public function register(RegisterRequest $request)
    {
        try {

            return response()->json([
                'response_code' => 201,
                'status'        => 'success',
                'message'       => 'Successfully registered',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Registration failed',
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {

        try {
            $token = $this->authService->login($request);

            if (!$token) {
                return response()->json([
                    'response_code' => 401,
                    'status'        => 'error',
                    'message'       => 'Unauthorized',
                ], 401);
            }

            $user = Auth::user();

            return response()->json([
                'response_code' => 200,
                'status'        => 'success',
                'message'       => 'Login successful',
                'user_info'     => [
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
                'token'       => $token,
                'token_type'  => 'Bearer',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Login failed',
            ], 500);
        }
    }

    public function logOut(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'response_code' => 401,
                    'status'        => 'error',
                    'message'       => 'User not authenticated',
                ], 401);
            }

            $this->authService->logout($user);

            return response()->json([
                'response_code' => 200,
                'status'        => 'success',
                'message'       => 'Successfully logged out',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'An error occurred during logout',
            ], 500);
        }
    }
}