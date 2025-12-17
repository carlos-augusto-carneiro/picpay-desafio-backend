<?php

namespace App\Http\Controllers;

use App\CQRS\User\Commands\LoginCommand;
use App\CQRS\User\Handlers\LoginHandler;
use App\CQRS\User\Handlers\LogoutHandler;
use App\CQRS\User\Handlers\RefreshLoginHandler;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/login",
     * summary="Realiza o login e retorna o token JWT",
     * tags={"Auth"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="carlos@teste.com"),
     * @OA\Property(property="password", type="string", format="password", example="Senha123!")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Login realizado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbG..."),
     * @OA\Property(property="token_type", type="string", example="bearer"),
     * @OA\Property(property="expires_in", type="integer", example=3600)
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Credenciais inválidas"
     * )
     * )
     */
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

    /**
     * @OA\Post(
     * path="/api/logout",
     * summary="Desconecta o usuário (Invalida o token)",
     * tags={"Auth"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Logout realizado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Logged out successfully")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Token não fornecido ou inválido"
     * )
     * )
     */
    public function logout(LogoutHandler $handler)
    {
        $handler->handle();
        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * @OA\Post(
     * path="/api/refresh",
     * summary="Atualiza o token JWT expirado",
     * tags={"Auth"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Novo token gerado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbG..."),
     * @OA\Property(property="token_type", type="string", example="bearer"),
     * @OA\Property(property="expires_in", type="integer", example=3600)
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Token inválido ou não fornecido"
     * )
     * )
     */
    public function refreshLogin(Request $request, RefreshLoginHandler $handler)
    {
        $tokenData = $handler->handle();
        return response()->json($tokenData);
    }
}
