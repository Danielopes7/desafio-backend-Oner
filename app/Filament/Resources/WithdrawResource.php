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
use Filament\Tables\Actions\CreateAction;

class WithdrawResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $modelLabel = 'Withdraw';
    protected static ?string $navigationLabel = 'Withdraws';

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('amount')
                ->required()
                ->numeric()
                ->label('Withdraw Amount')
                ->gt("0"),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('amount')->label('Amount')->money('BRL'),
            Tables\Columns\TextColumn::make('status')
                ->icon(fn (string $state): string => match ($state) {
                'approved' => 'heroicon-o-check-circle',
                'pending'    => 'heroicon-o-ellipsis-horizontal-circle',
                default    => 'heroicon-o-question-mark-circle',
                })
                ->color(fn (string $state): string => match ($state) {
                'approved' => 'success',
                'pending'    => 'warning',
                default    => 'gray',
            }),
            Tables\Columns\TextColumn::make('created_at')->label('Withdraw Date')->dateTime(),
        ])
        ->defaultSort('created_at', 'desc')
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make()->hidden(),
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make('withdraw')
                ->label('Make Withdraw')
                ->model(\App\Models\Transaction::class)
                ->form([
                    Forms\Components\Placeholder::make('saldo_atual')
                        ->label('Balance Available')
                        ->content(fn () => ' R$ ' . Auth::user()->balance),
                    Forms\Components\TextInput::make('amount')
                        ->label('Withdraw amount')
                        ->required()
                        ->numeric()
                ])
                ->using(function (array $data, CreateAction $action)  {
                    try {
                        $withdraw = app(WithdrawAction::class)->handle((object) $data);
                        Notification::make()
                            ->title('Withdraw made successfully!')
                            ->success()
                            ->send();
    
                        return $withdraw;
                    } catch (\Throwable $e){
                        Notification::make()
                            ->title('Withdraw Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        $action->halt();
                    }
                })
                ->modalSubmitActionLabel('Withdraw')
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
