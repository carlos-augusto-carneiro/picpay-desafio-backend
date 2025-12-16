<?php

namespace App\CQRS\User\Handlers;

use Illuminate\Contracts\Auth;

class LogoutHandler
{
    public function handle(): void
    {
        auth()->logout();
    }
}
