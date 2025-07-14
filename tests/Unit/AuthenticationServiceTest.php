<?php

namespace Tests\Unit;

use App\Services\AuthenticationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_a_user()
    {
        $service = new AuthenticationService;
        $data = [
            'name' => 'Teste',
            'email' => 'teste@teste.com',
            'document' => '12345678900',
            'type' => 'customer',
            'password' => 'senha123',
        ];

        $user = $service->register($data);

        $this->assertDatabaseHas('users', [
            'email' => 'teste@teste.com',
        ]);
        $this->assertTrue(Hash::check('senha123', $user->password));
    }
}
