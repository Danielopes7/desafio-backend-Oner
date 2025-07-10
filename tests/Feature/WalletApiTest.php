<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WalletApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_deposit_increases_user_balance()
    {
        $user = User::factory()->create([
            'email' => 'deposit@example.com',
            'password' => Hash::make('password123'),
            'balance' => 100,
            'type' => 'customer',
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'deposit@example.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('token');

        $depositAmount = 150;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/deposit', [
            'amount' => $depositAmount,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertEquals(250, $user->fresh()->balance);
    }

    public function test_api_withdraw_decreases_user_balance()
    {
        $user = User::factory()->create([
            'email' => 'withdraw@example.com',
            'password' => Hash::make('password123'),
            'balance' => 300,
            'type' => 'customer',
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'withdraw@example.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('token');

        $withdrawAmount = 120;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/withdraw', [
            'amount' => $withdrawAmount,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',  
            ]);

        $this->assertEquals(180, $user->fresh()->balance);
    }
}