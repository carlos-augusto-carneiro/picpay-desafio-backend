<?php

namespace App\CQRS\User\Handlers;

use App\CQRS\User\Commands\LoginCommand;
use Tymon\JWTAuth\JWTGuard;

class LoginHandler
{
    public function handle(LoginCommand $command): array
    {
        /** @var JWTGuard $guard */
        $guard = auth()->guard('api');

        if(! $token = $guard->attempt([
            'email' => $command->email,
            'password' => $command->password,
        ])) {
            throw new \Exception('Unauthorized', 401);
        }
        return $this->respondWithToken($token);
    }

    public function respondWithToken($token): array
    {
        /** @var JWTGuard $guard */
        $guard = auth()->guard('api');
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $guard->factory()->getTTL() * 60
        ];
    }
}
