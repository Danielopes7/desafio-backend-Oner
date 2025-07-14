<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTransactionNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        private User $sender,
        private User $recipient,
        private $amount
    ) {}

    public function handle(): void
    {
        $this->sendNotification(
            to: $this->sender->email,
            senderName: $this->sender->name,
            recipientName: $this->recipient->name,
            message: "Voce transferiu R$ {$this->formatAmount()} para {$this->recipient->name}."
        );

        $this->sendNotification(
            to: $this->recipient->email,
            senderName: $this->sender->name,
            recipientName: $this->recipient->name,
            message: "Voce recebeu R$ {$this->formatAmount()} de {$this->sender->name}."
        );

        Log::info('Transaction notifications sent successfully.');
    }

    private function sendNotification(string $to, string $senderName, string $recipientName, string $message): void
    {
        $sender = mb_convert_encoding($senderName, 'UTF-8', 'UTF-8');
        $recipient = mb_convert_encoding($recipientName, 'UTF-8', 'UTF-8');
        $message = mb_convert_encoding($message, 'UTF-8', 'UTF-8');

        $response = Http::post('https://66ad1f3cb18f3614e3b478f5.mockapi.io/v1/send', [
            'sender' => $sender,
            'to' => $to,
            'recipient' => $recipient,
            'message' => $message,
        ]);

        if ($response->failed()) {
            Log::error('Error sending notification: '.$response->body());
        }

    }

    private function formatAmount(): string
    {
        return number_format($this->amount, 2, ',', '.');
    }
}
