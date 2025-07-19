<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Actions\WithdrawAction;

class WithdrawResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $modelLabel = 'Saque';
    protected static ?string $navigationLabel = 'Saques';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('amount')
                ->required()
                ->numeric()
                ->label('Valor do saque'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('amount')->label('Valor')->money('BRL'),
            Tables\Columns\TextColumn::make('status')->label('Status'),
            Tables\Columns\IconColumn::make('is_refunded')->label('Reembolsado')->boolean(),
            Tables\Columns\TextColumn::make('created_at')->label('Data')->dateTime(),
        ])
        ->defaultSort('created_at', 'desc')
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make()->hidden(),
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make('sacar')
                ->label('Sacar')
                ->model(\App\Models\Transaction::class)
                ->form([
                    Forms\Components\Placeholder::make('saldo_atual')
                        ->label('Saldo disponível')
                        ->content(fn () => ' R$ ' . Auth::user()->balance),
                    Forms\Components\TextInput::make('amount')
                        ->label('Valor do Saque')
                        ->required()
                        ->numeric()
                ])
                ->using(function (array $data): \Illuminate\Database\Eloquent\Model  {
                    $withdraw = app(WithdrawAction::class)->handle((object) $data);
                    Notification::make()
                        ->title('Saque realizado com sucesso!')
                        ->success()
                        ->send();

                    return $withdraw;
                })
                ->modalHeading('Novo Saque')
                ->createAnother(false)
                ->successNotification(null),
        ])
        ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdraws::route('/'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('type', 'withdraw')
            ->where('payer_id', Auth::id());
    }
}
