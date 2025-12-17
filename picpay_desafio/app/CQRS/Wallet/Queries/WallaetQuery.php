<?php

namespace App\CQRS\Wallet\Queries;

readonly class WallaetQuery
{
    public function __construct(public string $id)
    {
    }
}
