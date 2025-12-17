<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;


class SendNotificationJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, Dispatchable, SerializesModels;

    public $tries = 5;
    /**
     * Create a new job instance.
     */
    public function __construct(public Transaction $transaction)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Enviando notificação para a transação: " . $this->transaction->id);
        /** @var Response $response */
        $response = Http::withoutVerifying()->post('https://util.devi.tools/api/v1/notify');
        if ($response->failed()) {
            Log::error("Falha ao enviar notificação para a transação ID: {$this->transaction->id}");
            return;
        }

        Log::info("Notificação enviada com sucesso.");
    }
}
