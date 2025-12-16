<?php

namespace App\CQRS\User\Commands;

use App\Enums\CpfCnpjType;
use App\Enums\UserType;

readonly class CreateUserCommand
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $cpf_cnpj,
        public UserType $type ,
    ) {}
}
