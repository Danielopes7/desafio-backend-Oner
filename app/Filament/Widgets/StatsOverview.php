<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Balance', 'R$ '  . number_format(Auth::user()->balance, 2, ',', '.')),
            Stat::make('Transfers Sent', Auth::user()->type === 'shopkeeper'
                    ? 'Not available'
                    : 'R$ ' . number_format(
                        Transaction::where('payer_id', Auth::id())
                            ->where('type', 'transfer')
                            ->sum('amount'),
                        2,
                        ',',
                        '.'
                    )
                )
                ->description(Auth::user()->type === 'shopkeeper' ? 'Restricted' : 'Sent')
                ->descriptionIcon(Auth::user()->type === 'shopkeeper' ? 'heroicon-m-exclamation-circle' : 'heroicon-m-arrow-up-tray')
                ->color(Auth::user()->type === 'shopkeeper' ? 'danger' : 'danger'),
            Stat::make('Transfers Received', 'R$ ' . number_format(Transaction::where('payee_id', Auth::id())->where('type', 'transfer')->sum('amount'), 2, ',', '.'))
                ->description('Received')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('success'),
            Stat::make('Deposits Made', 'R$ ' . number_format(Transaction::where('payee_id', Auth::id())->where('type', 'deposit')->sum('amount'), 2, ',', '.'))
                ->description('Deposit')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('success'),
            Stat::make('Withdrawals Made', 'R$ ' . number_format(Transaction::where('payee_id', Auth::id())->where('type', 'withdraw')->sum('amount'), 2, ',', '.'))
                ->description('Withdraw')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('success'),
            Stat::make('Refunds Received', 'R$ ' . number_format(Transaction::where('payee_id', Auth::id())->where('type', 'refund')->sum('amount'), 2, ',', '.'))
                ->description('Withdraw')
                ->descriptionIcon('heroicon-m-arrows-right-left')
                ->color('warning'),
        ];
    }
}
