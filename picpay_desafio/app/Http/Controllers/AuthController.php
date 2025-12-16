<?php

namespace App\Http\Controllers;

use App\CQRS\User\Commands\LoginCommand;
use App\CQRS\User\Handlers\LoginHandler;
use App\CQRS\User\Handlers\LogoutHandler;
use App\CQRS\User\Handlers\RefreshLoginHandler;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request, LoginHandler $handler)
    {
        $credentials = $request->only(['email', 'password']);

        $command = new LoginCommand(
            email: $credentials['email'],
            password: $credentials['password'],
        );

        $handler->handle($command);
        $tokenData = $handler->handle($command);
        return response()->json($tokenData);
    }

    public function logout(LogoutHandler $handler)
    {
        $handler->handle();
        return response()->json(['message' => 'Logged out successfully']);
    }
    public function refreshLogin(RefreshLoginHandler $handler)
    {
        $tokenData = $handler->handle();
        return response()->json($tokenData);
    }
}
