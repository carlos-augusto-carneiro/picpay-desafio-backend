<?php

namespace App\CQRS\Wallet\Commands;

readonly class WithdrawMoneyCommand
{
    public function __construct(
        public float $amount,
        public string $walletId,
    ) {}
}
