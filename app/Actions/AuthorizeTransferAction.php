<?php

namespace App\Actions;

use Exception;
use Illuminate\Support\Facades\Http;

class AuthorizeTransferAction
{
    public function __invoke(array $payload): void
    {
        $response = Http::post('https://66ad1f3cb18f3614e3b478f5.mockapi.io/v1/auth', $payload);

        if ($response->failed()) {
            throw new Exception('Transfer not authorized.');
        }
    }
}
