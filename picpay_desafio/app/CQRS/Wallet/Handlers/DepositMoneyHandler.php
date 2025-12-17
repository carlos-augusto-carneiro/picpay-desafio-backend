<?php

namespace App\CQRS\Wallet\Handlers;

use App\CQRS\Wallet\Commands\DepositMoneyCommand;
use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class DepositMoneyHandler
{
    public function handle(DepositMoneyCommand $command)
    {
        return DB::transaction(function () use ($command) {
            $wallet = Wallet::findOrFail($command->walletId);

            $wallet->balance += $command->amount;
            $wallet->save();

            Transaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $command->amount,
                'type' => TransactionType::DEPOSIT,
                'wallet_id_destination' => null,
                'description' => 'Depósito via API'
            ]);

            return $wallet;
        });
    }
}
