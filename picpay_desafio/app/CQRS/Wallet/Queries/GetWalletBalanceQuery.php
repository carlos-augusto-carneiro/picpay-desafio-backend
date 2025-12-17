<?php

namespace App\CQRS\Wallet\Queries;

readonly class GetWalletBalanceQuery
{
    public function __construct(
        public string $walletId,
    ) {}
}
