<?php

namespace App\CQRS\Wallet\Handlers;

use App\CQRS\Wallet\Queries\WallaetQuery;
use App\Models\Wallet;
use OpenApi\Annotations\Get;

class WallaetHandler
{
    public function handle(WallaetQuery $query): Wallet
    {
        return Wallet::query()
            ->where('user_id', $query->id)
            ->firstOrFail();
    }
}
