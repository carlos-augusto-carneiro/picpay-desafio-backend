<?php

namespace App\CQRS\User\Handlers;
use Tymon\JWTAuth\JWTGuard;

class RefreshLoginHandler
{
    public function handle(): void
    {
        /** @var JWTGuard $guard */
        $guard = auth()->guard('api');
        $guard->refresh();
    }
}
