<?php

namespace App\CQRS\User\Handlers;

use App\Models\User;
use App\CQRS\User\Commands\CreateUserCommand;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;


class CreateUserHandler
{
    public function handle(CreateUserCommand $command): User
    {
        return DB::transaction(function () use ($command) {

            $user = User::create([
                'name' => $command->name,
                'email' => $command->email,
                'cpf_cnpj' => $command->cpf_cnpj,
                'password' => $command->password,
                'type' => $command->type,
            ]);

            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0.00
            ]);

            return $user;
        });
    }

}
