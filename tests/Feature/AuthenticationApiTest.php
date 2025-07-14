<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_api_register_creates_user()
    {
        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'document' => $this->faker->numerify('###########'),
            'type' => 'customer',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'response_code',
                'status',
                'message',
                'user' => [
                    'name',
                    'email',
                ],
            ]);

        $teste = $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
        ]);
    }

    public function test_api_register_validation_fails_with_invalid_data()
    {
        $existingUser = User::factory()->create([
            'email' => 'teste@exemplo.com',
            'cpf_cnpj' => '12345678900',
        ]);

        $payload = [
            'name' => 'Jo',
            'email' => $existingUser->email,
            'document' => $existingUser->cpf_cnpj,
            'type' => 'admin',
            'password' => '123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                    'email',
                    'document',
                    'type',
                    'password',
                ],
            ]);

        $response->assertJsonValidationErrors([
            'name',
            'email',
            'document',
            'type',
            'password',
        ]);
    }

    public function test_api_login_returns_expected_response()
    {
        User::factory()->create([
            'name' => 'daniel',
            'email' => 'daniel@example.com',
            'password' => Hash::make('password@123'),
        ]);

        $payload = [
            'email' => 'daniel@example.com',
            'password' => 'password@123',
        ];

        $response = $this->postJson('/api/login', $payload);
        $response->assertStatus(200)
            ->assertJson([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Login successful',
                'user_info' => [
                    'name' => 'daniel',
                    'email' => 'daniel@example.com',
                ],
            ])
            ->assertJsonStructure([
                'response_code',
                'status',
                'message',
                'user_info' => [
                    'name',
                    'email',
                ],
                'token',
                'token_type',
            ]);
    }

    public function test_api_logout_revokes_token()
    {
        $user = User::factory()->create([
            'email' => 'davi@example.com',
            'password' => Hash::make('password@123'),
        ]);

        $loginPayload = [
            'email' => 'davi@example.com',
            'password' => 'password@123',
        ];

        $loginResponse = $this->postJson('/api/login', $loginPayload);
        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);

        $protectedResponse = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/transfer', [
            'payee_id' => 1,
            'amount' => 10,
        ]);
        $protectedResponse->assertStatus(400);
    }
}
