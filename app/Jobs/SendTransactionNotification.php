<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Bus\Dispatchable;

class SendTransactionNotification implements ShouldQueue
{
    use Queueable , Dispatchable;

    public function __construct(
        private User $sender,
        private User $recipient,
        private int $amount
    ) {}

    public function handle(): void
    {
        $message = "Você transferiu R$ {$this->amount} para {$this->recipient->name}.";
        
        $sender     = mb_convert_encoding($this->sender->name, 'UTF-8', 'UTF-8');
        $recipient  = mb_convert_encoding($this->recipient->name, 'UTF-8', 'UTF-8');
        $message    = mb_convert_encoding("Voce transferiu R$ {$this->amount} para {$recipient}.", 'UTF-8', 'UTF-8');

        $return = Http::post('https://66ad1f3cb18f3614e3b478f5.mockapi.io/v1/send', [
            'sender'     => $sender,
            'recipient'  => $recipient,
            'amount'     => $this->amount,
            'message'    => $message,
        ]);

        Log::info('Notificação enviada com sucesso.');
    }
}
