<?php

namespace App\CQRS\Wallet\Commands;

readonly class TransferMoneyCommand
{
    public function __construct(
        public float $amount,
        public string $walletIdpayer,
        public string $walletIdpayee,
    ) {}
}
