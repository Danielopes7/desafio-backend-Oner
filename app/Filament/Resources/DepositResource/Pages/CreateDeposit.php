<?php

namespace App\Filament\Resources\DepositResource\Pages;

use App\Filament\Resources\DepositResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDeposit extends CreateRecord
{
    protected static string $resource = DepositResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        dd($data); // Debugging line, can be removed later
        $data['type'] = 'deposit';
        $data['payee_id'] = Auth::id();
        $data['payer_id'] = Auth::id();
        $data['status'] = 'approved';

        return $data;
    }
}
