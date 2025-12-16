<?php

namespace App\CQRS\User\Handlers;
use App\CQRS\User\Commands\UpdateUserCommand;
use App\Models\User;

class UpdateUserHandler
{
    public function handle(UpdateUserCommand $command): User
    {
        $user = User::findOrFail($command->userId);

        if ($command->name !== null) {
            $user->name = $command->name;
        }
        if ($command->email !== null) {
            $user->email = $command->email;
        }
        if ($command->password !== null) {
            $user->password = bcrypt($command->password);
        }

        $user->save();

        return $user;
    }
}
