<?php

namespace App\CQRS\User\Commands;

readonly class DeleteUserCommand
{
    public function __construct(
        public int $userId
    ) {}
}
