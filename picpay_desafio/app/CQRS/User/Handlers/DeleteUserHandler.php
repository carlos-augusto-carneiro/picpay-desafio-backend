<?php

namespace App\CQRS\User\Handlers;

use App\CQRS\User\Commands\DeleteUserCommand;
use App\Models\User;

class DeleteUserHandler
{
    public function handle(DeleteUserCommand $command): void
    {
        $user = User::findOrFail($command->userId);
        $user->delete();
    }
}
