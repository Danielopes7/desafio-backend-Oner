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
use Filament\Notifications\Notification;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('transfer')
                ->label('Make Transfer')
                ->visible(fn () => auth()->user()?->type !== 'shopkeeper')
                ->model(Transaction::class)
                ->form([
                    Select::make('payee_id')
                        ->label('Payee')
                        ->options(User::query()
                            ->where('id', '<>', auth()->id())
                            ->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    TextInput::make('amount')
                        ->label('Transfer Amount')
                        ->required()
                        ->numeric(),
                ])
                ->using(function (array $data, CreateAction $action) {
                    try{
                        return app(\App\Services\TransferService::class)
                            ->execTransfer((object) $data);

                    }catch (\Throwable $e){
                        Notification::make()
                            ->title('Erro ao realizar a transferência')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        $action->halt();
                    }
                })
                ->modalHeading('New Transfer')
                ->modalSubmitActionLabel('Transfer')
                ->createAnother(false)
                ->successNotificationTitle('Transferência realizada com sucesso!')
                ->successRedirectUrl(null),
        ];
    }
}
