<?php

namespace App\Http\Controllers;

use App\CQRS\User\Commands\CreateUserCommand;
use App\CQRS\User\Commands\DeleteUserCommand;
use App\CQRS\User\Commands\UpdateUserCommand;
use App\Http\Requests\UpdateUserRequest;
use App\CQRS\User\Queries\GetUserQuery;
use App\CQRS\User\Handlers\CreateUserHandler;
use App\CQRS\User\Handlers\DeleteUserHandler;
use App\CQRS\User\Handlers\GetAllUserHandler;
use App\CQRS\User\Handlers\GetUserHandler;
use App\CQRS\User\Handlers\UpdateUserHandler;
use App\Enums\CpfCnpjType;
use App\Enums\UserType;
use App\Http\Requests\StoreUserRequest;

class UserController extends Controller
{
    public function index(GetAllUserHandler $handler)
    {
        $users = $handler->handle();
        return response()->json($users);
    }

    public function store(StoreUserRequest $request, CreateUserHandler $handler)
    {
        $data = $request->validated();

        $command = new CreateUserCommand(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            cpf_cnpj: CpfCnpjType::from($data['cpf_cnpj']),
            type: UserType::from($data['type']),
        );
        $user = $handler->handle($command);
        return response()->json($user, 201);
    }

    public function show(int $id, GetUserHandler $handler)
    {
        $query = new GetUserQuery(userId: $id);
        $user = $handler->handle($query);
        return response()->json($user);
    }

    public function update(int $id, UpdateUserHandler $handler, UpdateUserRequest $request)
    {
        $data = $request->validated();

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

    public function destroy(int $id, DeleteUserHandler $handler)
    {
        $command = new DeleteUserCommand(userId: $id);
        $handler->handle($command);
        return response()->json(['message' => "User with ID: {$command->userId} deleted successfully"]);
    }
}
