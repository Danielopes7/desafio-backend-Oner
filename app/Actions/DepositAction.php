<?php

namespace App\Actions;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepositAction
{
    public function handle(object $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $user_id = Auth::id();
            $user = User::findOrFail($user_id);

            $user->increment('balance', $data->amount);

            return Transaction::create([
                'payer_id' => $user->id,
                'payee_id' => $user->id,
                'type' => TransactionType::DEPOSIT,
                'amount' => $data->amount,
                'status' => TransactionStatus::APPROVED,
            ]);
        });
    }
}
