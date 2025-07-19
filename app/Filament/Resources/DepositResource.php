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
    protected static ?string $modelLabel = 'Deposit';
    protected static ?string $navigationLabel = 'Deposits';

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('amount')
                ->required()
                ->numeric()
                ->label('Deposit Amount'),
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
            Tables\Columns\TextColumn::make('created_at')->label('Deposit Date')->dateTime(),
        ])
        ->defaultSort('created_at', 'desc')
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make()->hidden(), // ocultar edióóo
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make('deposit')
                ->label('Make Deposit')
                ->model(\App\Models\Transaction::class)
                ->form([
                    Forms\Components\TextInput::make('amount')
                        ->label('Deposit Amount')
                        ->required()
                        ->numeric()
                        ->gt("0"),
                ])
                ->using(function (array $data): \Illuminate\Database\Eloquent\Model {
                    $deposit = app(DepositAction::class)->handle((object) $data);

                    Notification::make()
                        ->title('Deposit made successfully!')
                        ->success()
                        ->send();

                    return $deposit;
                })
                ->modalSubmitActionLabel('Deposit')
                ->modalHeading('New Deposit')
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
