<?php

namespace App\Actions;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class DepositAction
{
    public function handle(object $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            if ($data->amount <= 0){
                throw new Exception('Amount must be greater than 0.');
            }

            User::where('id', Auth::id())->increment('balance', $data->amount);
            
            return Transaction::create([
                'payer_id' => Auth::id(),
                'payee_id' => Auth::id(),
                'type' => TransactionType::DEPOSIT,
                'amount' => $data->amount,
                'status' => TransactionStatus::APPROVED,
            ]);
        });
    }
}
