<?php

namespace App\Http\Controllers;

use App\CQRS\User\Commands\CreateUserCommand;
use App\CQRS\User\Commands\DeleteUserCommand;
use App\CQRS\User\Commands\GetAllUserCommand;
use App\CQRS\User\Commands\UpdateUserCommand;
use App\Http\Requests\UpdateUserRequest;
use App\CQRS\User\Queries\GetUserQuery;
use App\CQRS\User\Handlers\CreateUserHandler;
use App\CQRS\User\Handlers\DeleteUserHandler;
use App\CQRS\User\Handlers\GetAllUserHandler;
use App\CQRS\User\Handlers\GetUserHandler;
use App\CQRS\User\Handlers\UpdateUserHandler;
use App\Enums\UserType;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/users",
     * summary="Lista todos os usuários",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="quantity",
     * in="query",
     * description="Quantidade de itens por página",
     * required=false,
     * @OA\Schema(type="integer", default=15)
     * ),
     * @OA\Response(
     * response=200,
     * description="Lista de usuários retornada com sucesso",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="data", type="array", @OA\Items(
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="Carlos Freitas"),
     * @OA\Property(property="email", type="string", example="carlos@teste.com"),
     * @OA\Property(property="type", type="string", example="user")
     * )),
     * @OA\Property(property="current_page", type="integer", example=1),
     * @OA\Property(property="per_page", type="integer", example=15)
     * )
     * )
     * )
     */
    public function index(Request $request, GetAllUserHandler $handler)
    {
        /*@var int $quantity */
        $quantity = $request->query('quantity', 15);
        $command = new GetAllUserCommand(quantity: $quantity);
        $users = $handler->handle($command);
        return response()->json($users);
    }

    /**
     * @OA\Post(
     * path="/api/users",
     * summary="Cria um novo usuário",
     * tags={"Users"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name","email","password","cpf_cnpj","type"},
     * @OA\Property(property="name", type="string", example="Carlos Freitas"),
     * @OA\Property(property="email", type="string", format="email", example="carlos@teste.com"),
     * @OA\Property(property="password", type="string", format="password", example="Senha123!"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="Senha123!"),
     * @OA\Property(property="cpf_cnpj", type="string", example="12345678900"),
     * @OA\Property(property="type", type="string", enum={"user", "lojista"}, example="user")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Usuário criado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="Carlos Freitas"),
     * @OA\Property(property="email", type="string", example="carlos@teste.com"),
     * @OA\Property(property="wallet", type="object",
     * @OA\Property(property="balance", type="number", example=0.00)
     * )
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Erro de validação"
     * )
     * )
     */
    public function store(StoreUserRequest $request, CreateUserHandler $handler)
    {
        $data = $request->validated();

        $command = new CreateUserCommand(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            cpf_cnpj: $data['cpf_cnpj'],
            type: UserType::from($data['type']),
        );
        $user = $handler->handle($command);
        return response()->json($user, 201);
    }

    /**
     * @OA\Get(
     * path="/api/users/{id}",
     * summary="Exibe os dados de um usuário específico",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID do usuário",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Dados do usuário encontrados",
     * @OA\JsonContent(
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="Carlos Freitas"),
     * @OA\Property(property="email", type="string", example="carlos@teste.com"),
     * @OA\Property(property="cpf_cnpj", type="string", example="12345678900"),
     * @OA\Property(property="wallet", type="object",
     * @OA\Property(property="balance", type="number", example=50.00)
     * )
     * )
     * ),
     * @OA\Response(response=404, description="Usuário não encontrado")
     * )
     */
    public function show(string $id, GetUserHandler $handler)
    {
        $query = new GetUserQuery(userId: $id);
        $user = $handler->handle($query);
        return response()->json($user);
    }

    /**
     * @OA\Put(
     * path="/api/users/{id}",
     * summary="Atualiza um usuário existente",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID do usuário",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string", example="Carlos Editado"),
     * @OA\Property(property="email", type="string", format="email", example="novo@email.com"),
     * @OA\Property(property="password", type="string", format="password", example="NovaSenha123!")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Usuário atualizado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="User updated successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="Carlos Editado"),
     * @OA\Property(property="email", type="string", example="novo@email.com")
     * )
     * )
     * ),
     * @OA\Response(response=404, description="Usuário não encontrado"),
     * @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function update(string $id, UpdateUserHandler $handler, UpdateUserRequest $request)
    {
        $data = $request->validated();

        $senderWallet = $request->user()->id;
        if ($senderWallet !== $id) {
            abort(422, 'Você não pode atualizar um usuário que não é você.');
        }
        $command = new UpdateUserCommand(
            userId: $id,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
        );

        $user = $handler->handle($command);
        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/users/{id}",
     * summary="Remove um usuário",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID do usuário",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Usuário removido com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="User with ID: 1 deleted successfully")
     * )
     * ),
     * @OA\Response(response=404, description="Usuário não encontrado")
     * )
     */
    public function destroy(string $id, Request $request, DeleteUserHandler $handler)
    {
        $command = new DeleteUserCommand(userId: $id);
        $senderWallet = $request->user()->id;
        if ($senderWallet !== $id) {
            abort(422, 'Você não pode deletar um usuário que não é você.');
        }
        $handler->handle($command);
        return response()->json(['message' => "User with ID: {$command->userId} deleted successfully"]);
    }
}
