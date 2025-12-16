<?php

namespace App\CQRS\User\Handlers;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class GetAllUserHandler
{
    public function handle(): Collection
    {
        return User::with('wallet')->get();
    }
}
