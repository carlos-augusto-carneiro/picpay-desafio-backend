<?php

namespace App\CQRS\User\Commands;

readonly class UpdateUserCommand
{
    public function __construct(
        public string $userId,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
    ) {}
}
