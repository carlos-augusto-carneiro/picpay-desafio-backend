<?php

namespace App\CQRS\User\Handlers;

use App\CQRS\User\Queries\GetUserQuery;
use App\Models\User;

class GetUserHandler
{
    public function handle(GetUserQuery $query): User
    {
        return User::with('wallet')->findOrFail($query->userId);
    }
}
