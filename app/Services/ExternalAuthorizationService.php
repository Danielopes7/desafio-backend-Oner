<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class ExternalAuthorizationService
{
    public function authorizeTransfer(): void
    {
        $response = Http::get('https://66ad1f3cb18f3614e3b478f5.mockapi.io/v1/auth'); // URL mock

        if (! $response->ok() || $response->json('message') !== 'Autorizado') {
            throw new Exception('Transfer not authorized.');
        }
    }
}
