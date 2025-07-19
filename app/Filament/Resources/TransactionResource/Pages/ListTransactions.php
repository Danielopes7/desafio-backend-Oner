<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\User;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('transferir')
                ->label('Transferir')
                ->visible(fn () => auth()->user()?->type !== 'shopkeeper')
                ->model(Transaction::class)
                ->form([
                    Select::make('payee_id')
                        ->label('Destinatário')
                        ->options(User::query()
                            ->where('id', '<>', auth()->id())
                            ->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    TextInput::make('amount')
                        ->label('Valor da Transferência')
                        ->required()
                        ->numeric(),
                ])
                ->using(function (array $data) {
                    return app(\App\Services\TransferService::class)
                        ->execTransfer((object) $data);
                })
                ->createAnother(false)
                ->successNotificationTitle('Transferência realizada com sucesso!')
                ->successRedirectUrl(null),
        ];
    }
}
