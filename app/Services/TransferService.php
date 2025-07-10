<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Enums\TransactionType;
use App\Enums\TransactionStatus;

use Exception;

class TransferService
{
    public function execTransfer($data): Transaction
    {
        DB::beginTransaction();

        try {
            $user_id = Auth::id();
            $sender = User::findOrFail($user_id);

            if (!$sender->canTransfer()) {
                throw new Exception('Only customers are allowed to make transfers.');
            }
            
            if ($user_id == $data->payee_id) {
                throw new Exception('Not allowed to transfer to yourself.');
            }
            
            if ($sender->balance < $data->amount) {
                throw new Exception('Insufficient balance.');
            }
            

            $recipient = User::findOrFail($data->payee_id);

            $sender->decrement('balance', $data->amount);
            $recipient->increment('balance', $data->amount);

            $transaction = Transaction::create([
                'payer_id' => $sender->id,
                'payee_id' => $recipient->id,
                'type'     => TransactionType::TRANSFER,
                'amount'   => $data->amount,
                'status'   => TransactionStatus::APPROVED,
            ]);

            // Notificar (mock ou real)
            // Notification::send($recipient, new TransferReceivedNotification(...));

            DB::commit();

            return $transaction;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function execRefund($data): Transaction 
    {
        DB::beginTransaction();

        try {
            $user_id = Auth::id();
            $sender = User::findOrFail($user_id);

            if (!$sender->canRefund()) {
                throw new Exception('Only shopkeeper are allowed to make refunds.');
            }

            $Transaction_refund = Transaction::where('id', $data->transaction_id)
                ->where('payee_id', $user_id)
                ->where('status', TransactionStatus::APPROVED)
                ->where('type', TransactionType::TRANSFER)
                ->first();

            if (!$Transaction_refund) {
                throw new Exception('Transaction not found or not eligible for refund.');
            }
            
            $recipient = User::findOrFail($Transaction_refund->payer_id);

            $recipient->increment('balance', $Transaction_refund->amount);
            $sender->decrement('balance', $Transaction_refund->amount);

            $transaction = Transaction::create([
                'payer_id' => $sender->id,
                'payee_id' => $recipient->id,
                'type'     => TransactionType::REFUND,
                'amount'   => $Transaction_refund->amount,
                'status'   => TransactionStatus::APPROVED,
            ]);

            // Notificar (mock ou real)
            // Notification::send($sender, new TransferRefundedNotification(...));

            DB::commit();

            return $transaction;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
