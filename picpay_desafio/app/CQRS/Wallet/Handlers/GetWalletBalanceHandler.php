<?php

namespace App\CQRS\Wallet\Handlers;

use App\CQRS\Wallet\Queries\GetWalletBalanceQuery;
use App\Models\Wallet;
class GetWalletBalanceHandler
{
    public function handle(GetWalletBalanceQuery $query): array
    {
        return Wallet::where('id', $query->walletId)
            ->select('id', 'balance')
            ->first()
            ?->toArray() ?? [];
    }
}
