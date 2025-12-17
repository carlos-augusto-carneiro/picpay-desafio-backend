<?php

namespace App\CQRS\User\Handlers;

use Illuminate\Contracts\Auth;
use Tymon\JWTAuth\JWTGuard;

class LogoutHandler
{
    public function handle(): void
    {
        /** @var JWTGuard $guard */
        $guard = auth()->guard('api'); 
        $guard->logout();
    }
}
