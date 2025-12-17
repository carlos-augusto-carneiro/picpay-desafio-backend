<?php

namespace App\CQRS\Wallet\Queries;

readonly class GetWalletStatementQuery
{
    public function __construct(
        public string $walletId,
        public string $startDate,
        public string $endDate,
    ) {}
}
