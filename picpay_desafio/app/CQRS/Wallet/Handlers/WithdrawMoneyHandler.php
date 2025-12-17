<?php

namespace App\CQRS\Wallet\Handlers;

use App\CQRS\Wallet\Commands\WithdrawMoneyCommand;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Wallet;

class WithdrawMoneyHandler
{
    public function handle(WithdrawMoneyCommand $command)
    {
        return DB::transaction(function () use ($command) {
            $wallet = Wallet::findOrFail($command->walletId);
            $wallet->balance -= $command->amount;
            if($wallet->balance < 0) {
                abort(422, 'Saldo insuficiente para saque.');
            }
            $wallet->save();

            Transaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $command->amount,
                'type' => 'withdraw',
                'wallet_id_destination' => null,
                'description' => 'Saque via API'
            ]);

            return $wallet;
        });

    }
}
