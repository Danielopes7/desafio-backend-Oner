<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthenticationService
{
    public function register($data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'cpf_cnpj' => $data['document'],
            'type'     => $data['type'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function login($credentials): ?string
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = Auth::user();
        return $user->createToken('authToken')->plainTextToken;
    }

    public function logout($user): void
    {
        $user->tokens()->delete();
    }
}
