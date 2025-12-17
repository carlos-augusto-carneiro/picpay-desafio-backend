<?php

namespace App\CQRS\User\Handlers;

use App\CQRS\User\Commands\GetAllUserCommand;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GetAllUserHandler
{
    public function handle(GetAllUserCommand $command): LengthAwarePaginator
    {
        return User::paginate($command->quantity);
    }
}
