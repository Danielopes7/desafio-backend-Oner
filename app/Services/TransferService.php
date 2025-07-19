<?php

namespace App\Services;

use App\Actions\AuthorizeTransferAction;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Jobs\SendTransactionNotification;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferService
{
    public function __construct(
        private AuthorizeTransferAction $authorizeTransfer
    ) {}

    public function execTransfer($data): Transaction
    {
        DB::beginTransaction();

        try {
            $user_id = Auth::id();
            $sender = User::findOrFail($user_id);

            if ($data->amount <= 0){
                throw new Exception('Amount must be greater than 0.');
            }

            if (! $sender->canTransfer()) {
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

            ($this->authorizeTransfer)([
                'origin' => [
                    'cpfcnpj' => $sender->cpf_cnpj,
                    'name' => $sender->name,
                    'balance' => number_format($sender->balance, 2, '.', ''),
                ],
                'destination' => [
                    'cpfcnpj' => $recipient->cpf_cnpj,
                    'name' => $recipient->name,
                    'balance' => number_format($recipient->balance, 2, '.', ''),
                ],
                'amount' => $data->amount,
            ]);

            $transaction = Transaction::create([
                'payer_id' => $sender->id,
                'payee_id' => $recipient->id,
                'type' => TransactionType::TRANSFER,
                'amount' => $data->amount,
                'status' => TransactionStatus::APPROVED,
            ]);

            DB::commit();
            SendTransactionNotification::dispatch($sender, $recipient, $data->amount);

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

            $transaction_refund = Transaction::where('id', $data->transaction_id ?? $data->id)
                ->where('payee_id', $user_id)
                ->where('status', TransactionStatus::APPROVED)
                ->where('type', TransactionType::TRANSFER)
                ->first();

            if ($sender->balance < $transaction_refund->amount) {
                throw new Exception('Insufficient balance to refund.');
            }

            if ($transaction_refund && $transaction_refund->is_refunded) {
                throw new Exception('This transaction has already been refunded.');
            }

            if (! $transaction_refund) {
                throw new Exception('Transaction not found or not eligible for refund.');
            }

            $recipient = User::findOrFail($transaction_refund->payer_id);

            $recipient->increment('balance', $transaction_refund->amount);
            $sender->decrement('balance', $transaction_refund->amount);

            $transaction = Transaction::create([
                'payer_id' => $sender->id,
                'payee_id' => $recipient->id,
                'type' => TransactionType::REFUND,
                'amount' => $transaction_refund->amount,
                'status' => TransactionStatus::APPROVED,
            ]);
            $transaction_refund->is_refunded = true;
            $transaction_refund->save();

            DB::commit();

            return $transaction;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
