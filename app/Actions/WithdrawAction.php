<?php

namespace App\Actions;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawAction
{
    public function handle(object $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $user_id = Auth::id();
            $user = User::findOrFail($user_id);

            if ($user->balance < $data->amount) {
                throw new Exception('Insufficient balance.');
            }

            $user->decrement('balance', $data->amount);

            return Transaction::create([
                'payer_id' => $user->id,
                'payee_id' => $user->id,
                'type' => TransactionType::WITHDRAW,
                'amount' => $data->amount,
                'status' => TransactionStatus::APPROVED,
            ]);
        });
    }
}
