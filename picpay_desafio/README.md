<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


# PicPay Simplificado - API Backend

API RESTful desenvolvida para o desafio PicPay, implementando uma plataforma de pagamentos simplificada com transferências entre usuários e lojistas.

## Sobre o Projeto

O **PicPay Simplificado** é uma plataforma de pagamentos que permite:
- Cadastro de usuários comuns e lojistas
- Depósitos e saques em carteiras digitais
- Transferências P2P (peer-to-peer) entre usuários
- Transferências de usuários para lojistas
- Consulta de saldo e extrato de transações

## Arquitetura e Tecnologias

### Stack Tecnológica

- **Framework**: Laravel 12 (PHP 8.2+)
- **Banco de Dados**: SQLite (desenvolvimento) e MySql no container Docker
- **Autenticação**: JWT (tymon/jwt-auth)
- **Docker**: Docker e Docker Compose
- **Documentação API**: Swagger/OpenAPI (l5-swagger)
- **Fila de Jobs**: Laravel Queue (para notificações assíncronas)

### Arquitetura Implementada

O projeto foi desenvolvido seguindo o padrão **CQRS (Command Query Responsibility Segregation)**, separando claramente as operações de escrita (Commands) e leitura (Queries):

```
app/
├── CQRS/
│   ├── User/          # Comandos e Queries para Usuários
│   │   ├── Commands/  # CreateUserCommand, UpdateUserCommand, etc.
│   │   ├── Handlers/  # Handlers que processam os comandos
│   │   └── Queries/   # Queries para consultas
│   └── Wallet/        # Comandos e Queries para Carteiras
│       ├── Commands/  # TransferMoneyCommand, DepositMoneyCommand, etc.
│       ├── Handlers/  # Handlers que processam os comandos
│       └── Queries/   # Queries para consultas
├── Enums/             # Enumerações (UserType, TransactionType)
├── Http/
│   ├── Controllers/   # Controllers RESTful
│   └── Requests/     # Form Requests para validação
├── Jobs/              # Jobs assíncronos (SendNotificationJob)
├── Models/            # Eloquent Models
└── Service/           # Serviços externos (AuthorizationService)
```

### Design Patterns Aplicados

- **CQRS**: Separação de comandos e queries
- **Command Pattern**: Encapsulamento de operações em objetos Command
- **Handler Pattern**: Processamento de comandos/queries por handlers dedicados
- **Service Layer**: Serviços para integrações externas
- **Repository Pattern**: Abstração de acesso a dados através dos Models
- **Job Queue Pattern**: Processamento assíncrono de notificações

##  Requisitos Implementados

### Regras de Negócio Atendidas

-  **Cadastro de Usuários**: Suporte para usuários comuns e lojistas com validação de CPF/CNPJ e e-mail únicos
-  **Tipos de Usuário**: Sistema diferencia entre `user` e `lojista`
-  **Carteiras Digitais**: Cada usuário possui uma carteira criada automaticamente no cadastro
-  **Transferências**: Usuários podem transferir para outros usuários e lojistas
-  **Restrição de Lojistas**: Lojistas **não podem** realizar transferências (apenas recebem)
-  **Validação de Saldo**: Verificação de saldo suficiente antes de transferências e saques
-  **Autorização Externa**: Integração com serviço mock de autorização (`https://util.devi.tools/api/v2/authorize`)
-  **Transações Atômicas**: Operações de transferência são transacionais (rollback em caso de erro)
-  **Notificações Assíncronas**: Envio de notificações via Job Queue para serviço externo (`https://util.devi.tools/api/v1/notify`)
-  **API RESTful**: Endpoints seguindo padrões REST

### Funcionalidades Extras Implementadas

-  **Autenticação JWT**: Sistema completo de autenticação com tokens JWT
-  **Extrato de Transações**: Consulta de histórico de transações por período
-  **Consulta de Saldo**: Endpoint para verificar saldo atual da carteira
-  **Depósitos e Saques**: Operações de depósito e saque além das transferências
-  **Documentação Swagger**: API documentada com Swagger/OpenAPI
-  **Circuit Breaker**: Implementação básica de circuit breaker para serviço de autorização
-  **Validações**: Validações robustas em todas as operações
-  **Segurança**: Validação de propriedade de carteiras (usuários só acessam suas próprias carteiras)

## 🚀 Como Executar

### Pré-requisitos

- PHP 8.2 ou superior
- Composer
- SQLite (ou configurar outro banco de dados)

### Instalação

1. **Clone o repositório**:
```bash
git clone <url-do-repositorio>
cd picpay-desafio-backend
```

2. **Entre no diretório do projeto Laravel**:
```bash
cd picpay_desafio
```

3. **Instale as dependências**:
```bash
composer install
```

4. **Configure o ambiente**:
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure o banco de dados** no arquivo `.env`:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/caminho/absoluto/para/database/database.sqlite
```

6. **Execute as migrations**:
```bash
php artisan migrate
```

7. **Gere a documentação Swagger** (opcional):
```bash
php artisan l5-swagger:generate
```

8. **Inicie o servidor**:
```bash
php artisan serve
```

9. **Inicie o worker de filas** (em outro terminal):
```bash
php artisan queue:work
```

A API estará disponível em `http://localhost:8000`

## 📚 Endpoints da API

### Autenticação

- `POST /api/login` - Realizar login e obter token JWT
- `POST /api/logout` - Fazer logout (requer autenticação)
- `POST /api/refresh` - Renovar token JWT (requer autenticação)

### Usuários

- `POST /api/users` - Criar novo usuário (público)
- `GET /api/users` - Listar usuários (requer autenticação)
- `GET /api/users/{id}` - Obter usuário específico (requer autenticação)
- `PUT /api/users/{id}` - Atualizar usuário (requer autenticação)
- `DELETE /api/users/{id}` - Deletar usuário (requer autenticação)

### Carteiras

- `POST /api/wallet/deposit` - Realizar depósito (requer autenticação)
- `POST /api/wallet/withdraw` - Realizar saque (requer autenticação)
- `POST /api/wallet/transfer` - Realizar transferência (requer autenticação)
- `GET /api/wallet/{id}` - Obter detalhes da carteira (requer autenticação)
- `GET /api/wallet/{id}/balance` - Consultar saldo (requer autenticação)
- `GET /api/wallet/{id}/statement` - Consultar extrato (requer autenticação)

### Documentação Swagger

- `GET /api/documentation` - Acessar documentação interativa da API

## 📝 Exemplos de Uso

### Criar Usuário

```http
POST /api/users
Content-Type: application/json

{
  "name": "João Silva",
  "email": "joao@example.com",
  "cpf_cnpj": "12345678900",
  "password": "senha123",
  "type": "user"
}
```

### Realizar Transferência

```http
POST /api/wallet/transfer
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 100.50,
  "wallet_id_source": "uuid-da-carteira-origem",
  "wallet_id_destination": "uuid-da-carteira-destino"
}
```

### Consultar Extrato

```http
GET /api/wallet/{id}/statement?start_date=2024-01-01&end_date=2024-12-31
Authorization: Bearer {token}
```

## 🗄️ Estrutura do Banco de Dados

### Tabelas Principais

- **users**: Armazena informações dos usuários (comuns e lojistas)
- **wallets**: Carteiras digitais vinculadas aos usuários
- **transactions**: Histórico de todas as transações (depósitos, saques, transferências)

### Relacionamentos

- Um usuário possui uma carteira (`User` → `Wallet` 1:1)
- Uma carteira possui múltiplas transações (`Wallet` → `Transaction` 1:N)
- Transações podem referenciar carteira de destino (`Transaction` → `Wallet` N:1)

## 🔧 Funcionalidades Técnicas

### Transações de Banco de Dados

Todas as operações financeiras utilizam transações do banco de dados para garantir atomicidade:
- Em caso de erro, todas as alterações são revertidas automaticamente
- Uso de `lockForUpdate()` para prevenir condições de corrida em transferências

### Processamento Assíncrono

Notificações são enviadas de forma assíncrona através de Jobs:
- `SendNotificationJob` processa o envio de notificações em background
- Retry automático em caso de falha (até 5 tentativas)
- Logs detalhados para rastreamento

### Circuit Breaker

Implementação básica de circuit breaker no `AuthorizationService`:
- Após 5 falhas consecutivas, o serviço entra em estado "open"
- Timeout de 30 segundos antes de tentar novamente
- Previne sobrecarga do serviço externo

## 📊 Cobertura de Requisitos do Desafio

| Requisito | Status | Observações |
|-----------|--------|-------------|
| Cadastro de usuários e lojistas | ✅ | Com validação de CPF/CNPJ e e-mail únicos |
| Transferências entre usuários | ✅ | Implementado com validações completas |
| Lojistas só recebem | ✅ | Validação impede lojistas de transferir |
| Validação de saldo | ✅ | Verificação antes de transferências e saques |
| Serviço autorizador externo | ✅ | Integração com mock service |
| Transações atômicas | ✅ | Uso de DB::transaction() |
| Notificações assíncronas | ✅ | Jobs com retry automático |
| API RESTful | ✅ | Endpoints seguindo padrões REST |

## Diferenciais Implementados

- **Arquitetura CQRS**: Separação clara entre comandos e queries
- **Design Patterns**: Aplicação de vários padrões de projeto
- **Documentação Swagger**: API completamente documentada
- **Validações Robustas**: Form Requests para validação de dados
- **Segurança**: Validação de propriedade de recursos
- **Código Limpo**: Estrutura organizada e desacoplada
- **Enums**: Uso de enums para tipos de usuário e transação
- **Jobs Assíncronos**: Processamento em background
- **Circuit Breaker**: Proteção contra falhas de serviços externos

## Estrutura de Diretórios

```
picpay_desafio/
├── app/
│   ├── CQRS/              # Arquitetura CQRS
│   │   ├── User/          # Domínio de Usuários
│   │   └── Wallet/        # Domínio de Carteiras
│   ├── Enums/             # Enumerações
│   ├── Http/              # Controllers e Requests
│   ├── Jobs/              # Jobs assíncronos
│   ├── Models/            # Models Eloquent
│   └── Service/           # Serviços externos
├── database/
│   ├── migrations/        # Migrations do banco
│   └── seeders/          # Seeders (se houver)
├── routes/
│   └── api.php           # Rotas da API
├── tests/                # Testes (estrutura preparada)
└── storage/
    └── api-docs/         # Documentação Swagger gerada
```

## Testes

A estrutura de testes está preparada. Para executar:

```bash
php artisan test
```

## Documentação Adicional

- Acesse a documentação Swagger em: `http://localhost:8000/api/documentation`
- Documentação do Laravel: https://laravel.com/docs

## Segurança

- Senhas são hasheadas usando bcrypt
- Autenticação via JWT tokens
- Validação de propriedade de recursos (usuários só acessam seus próprios dados)
- Validação de entrada em todos os endpoints
- Proteção contra SQL Injection (Eloquent ORM)
- Proteção contra XSS (sanitização automática do Laravel)

## Melhorias Futuras

- [ ] Implementar testes unitários e de integração completos
- [X] Adicionar Docker/Docker Compose
- [ ] Implementar CI/CD
- [ ] Adicionar logging estruturado
- [ ] Implementar métricas e observabilidade
- [ ] Adicionar cache para consultas frequentes
- [ ] Implementar rate limiting
- [ ] Adicionar validação de CPF/CNPJ mais robusta
- [ ] Implementar eventos e listeners para melhor desacoplamento

## Licença

Este projeto foi desenvolvido como parte de um desafio técnico.

---

**Desenvolvido com ❤️ usando Laravel e arquitetura CQRS**
