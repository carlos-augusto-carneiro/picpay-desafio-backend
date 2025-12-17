<?php

namespace App\CQRS\Wallet\Handlers;

use App\CQRS\Wallet\Commands\TransferMoneyCommand;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use App\Service\AuthorizationService;
use App\Enums\TransactionType;
use App\Jobs\SendNotificationJob;

class TransferMoneyHandler
{
    public function __construct(protected AuthorizationService $authorizationService)
    {
    }
    public function handle(TransferMoneyCommand $command)
    {
        $payer = Wallet::with("user")->findOrFail($command->walletIdpayer);
        $payee = Wallet::with("user")->findOrFail($command->walletIdpayee);

        if($payer->user->type === 'store'){
            throw new \Exception("Lojistas não podem realizar transferências");
        }
        if($payer->balance < $command->amount){
            throw new \Exception("Saldo insuficiente para realizar a transferência");
        }

        $this->authorizationService->authorize();
        $transaction =DB::transaction(function () use ($command, $payer, $payee) {
            $payer->balance -= $command->amount;
            $payer->save();

            $payee->balance += $command->amount;
            $payee->save();

            return Transaction::create([
                'wallet_id' => $payer->id,
                'amount' => $command->amount,
                'type' => TransactionType::TRANSFER,
                'wallet_id_destination' => $payee->id,
                'description' => 'Transferência via API'
            ]);
        });

        SendNotificationJob::dispatch($transaction, "Você realizou uma transferência de {$command->amount} para {$payee->user->name}");
        return $transaction;
    }
}
