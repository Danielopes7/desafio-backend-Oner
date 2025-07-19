<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-up-down';
    protected static ?string $modelLabel = 'Transfers';
    protected static ?string $navigationLabel = 'Transfers';

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('payer_id')
                    ->numeric(),
                Forms\Components\TextInput::make('payee_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('type')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Toggle::make('is_refunded')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payer.name')
                    ->label('Payer Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payee.name')
                    ->label('Payee Name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->money('BRL')
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Transfer date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_refunded')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('refund')
                    ->label('Refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (\App\Models\Transaction $record) =>
                        !$record->is_refunded && $record->payee_id === auth()->id()
                    )
                    ->action(function (\App\Models\Transaction $record) {
                        try {
                            app(\App\Services\TransferService::class)->execRefund($record);

                            Notification::make()
                                ->title('Refund made successfully!')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Refund Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            // 'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['payer', 'payee'])
            ->where('type', 'transfer')
            ->where(function ($query) {
                $query->where('payer_id', Auth::id())
                    ->orWhere('payee_id', Auth::id());
        });
    }
}
