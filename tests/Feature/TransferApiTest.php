<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TransferApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_transfer_between_users()
    {
        $payer = User::factory()->create([
            'name' => 'customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
            'balance' => 1000,
            'type' => 'customer'
        ]);
        $payee = User::factory()->create([
            'name' => 'shopkeeper',
            'email' => 'shopkeeper@example.com',
            'password' => Hash::make('password123'),
            'balance' => 100,
            'type' => 'shopkeeper'
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'customer@example.com',
            'password' => 'password123',
        ]);
        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('token');

        $transferPayload = [
            'payee_id' => $payee->id,
            'amount' => 200,
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/transfer', $transferPayload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertEquals(800, $payer->fresh()->balance);
        $this->assertEquals(300, $payee->fresh()->balance);
    }

}