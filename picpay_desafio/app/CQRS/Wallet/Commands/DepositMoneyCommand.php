<?php

namespace App\CQRS\Wallet\Commands;

readonly class DepositMoneyCommand
{
    public function __construct(
        public float $amount,
        public string $walletId,
    ) {}
}
