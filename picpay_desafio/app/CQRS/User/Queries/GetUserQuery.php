<?php

namespace App\CQRS\User\Queries;

readonly class GetUserQuery
{
    public function __construct(
        public string $userId,
    ) {}
}
