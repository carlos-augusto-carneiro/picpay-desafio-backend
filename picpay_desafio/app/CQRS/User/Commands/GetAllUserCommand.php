<?php

namespace App\CQRS\User\Commands;

readonly class GetAllUserCommand
{
    public function __construct(public int $quantity)
    {
    }
}
