<?php

namespace App\Filament\Resources\WithdrawResource\Pages;

use App\Filament\Resources\WithdrawResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateWithdraw extends CreateRecord
{
    protected static string $resource = WithdrawResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'withdraw';
        $data['payer_id'] = Auth::id();
        $data['payee_id'] = Auth::id(); // destino poderia ser conta banc�ria externa
        $data['status'] = 'pending'; // pode ser pending at� aprova��o manual

        return $data;
    }
}
