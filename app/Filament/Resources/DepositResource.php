<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepositResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Actions\DepositAction;

class DepositResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    protected static ?string $modelLabel = 'Depósito';
    protected static ?string $navigationLabel = 'Depósitos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('amount')
                ->required()
                ->numeric()
                ->label('Valor do depósito'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('amount')->label('Valor')->money('BRL'),
            Tables\Columns\TextColumn::make('status')->label('Status'),
            Tables\Columns\TextColumn::make('created_at')->label('Data')->dateTime(),
        ])
        ->defaultSort('created_at', 'desc')
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make()->hidden(), // ocultar edióóo
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make('depositar')
                ->label('Depositar')
                ->model(\App\Models\Transaction::class)
                ->form([
                    Forms\Components\TextInput::make('amount')
                        ->label('Valor do Depósito')
                        ->required()
                        ->numeric(),
                ])
                ->using(function (array $data): \Illuminate\Database\Eloquent\Model {
                    $deposit = app(DepositAction::class)->handle((object) $data);

                    Notification::make()
                        ->title('Depósito realizado com sucesso!')
                        ->success()
                        ->send();

                    return $deposit;
                })
                ->modalHeading('Novo Depósito')
                ->createAnother(false)
                ->successNotification(null),
        ])
        ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeposits::route('/'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('type', 'deposit')
            ->where('payee_id', Auth::id());
    }
    

    

}
