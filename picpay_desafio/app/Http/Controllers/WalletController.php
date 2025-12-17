<?php

namespace App\Http\Controllers;

use App\CQRS\Wallet\Commands\DepositMoneyCommand;
use App\CQRS\Wallet\Commands\TransferMoneyCommand;
use App\CQRS\Wallet\Commands\WithdrawMoneyCommand;
use App\CQRS\Wallet\Handlers\DepositMoneyHandler;
use App\CQRS\Wallet\Handlers\GetWalletBalanceHandler;
use App\CQRS\Wallet\Handlers\GetWalletStatementHandler;
use App\CQRS\Wallet\Handlers\TransferMoneyHandler;
use App\CQRS\Wallet\Handlers\WallaetHandler;
use App\CQRS\Wallet\Handlers\WithdrawMoneyHandler;
use App\CQRS\Wallet\Queries\GetWalletBalanceQuery;
use App\CQRS\Wallet\Queries\GetWalletStatementQuery;
use App\CQRS\Wallet\Queries\WallaetQuery;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/wallet/deposit",
     * summary="Realiza um depósito na carteira",
     * tags={"Wallet"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"amount", "wallet_id"},
     * @OA\Property(property="wallet_id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     * @OA\Property(property="amount", type="number", format="float", example=100.50)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Depósito realizado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="id", type="string", format="uuid"),
     * @OA\Property(property="balance", type="number", example=100.50),
     * @OA\Property(property="user_id", type="string", format="uuid")
     * )
     * ),
     * @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function deposit(Request $request, DepositMoneyHandler $handler)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'wallet_id' => 'required|uuid|exists:wallets,id',
        ]);
        $senderWallet = $request->user()->wallet;
        if ($senderWallet->id !== $data['wallet_id']) {
            abort(422, 'Você não pode depositar em uma carteira que não é sua.');
        }
        $command = new DepositMoneyCommand($data['amount'], $data['wallet_id']);
        $wallet = $handler->handle($command);

        return response()->json($wallet);
    }

    /**
     * @OA\Post(
     * path="/api/wallet/withdraw",
     * summary="Realiza um saque da carteira",
     * tags={"Wallet"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"amount", "wallet_id"},
     * @OA\Property(property="wallet_id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     * @OA\Property(property="amount", type="number", format="float", example=50.00)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Saque realizado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="id", type="string", format="uuid"),
     * @OA\Property(property="balance", type="number", example=50.50)
     * )
     * ),
     * @OA\Response(response=422, description="Saldo insuficiente ou erro de validação")
     * )
     */
    public function withdraw(Request $request, WithdrawMoneyHandler $handler)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'wallet_id' => 'required|uuid|exists:wallets,id',
        ]);
        $senderWallet = $request->user()->wallet;
        if ($senderWallet->id !== $data['wallet_id']) {
            abort(422, 'Você não pode sacar da carteira que não é sua.');
        }
        $command = new WithdrawMoneyCommand($data['amount'], $data['wallet_id']);
        $wallet = $handler->handle($command);

        return response()->json($wallet);
    }

    /**
     * @OA\Post(
     * path="/api/wallet/transfer",
     * summary="Realiza transferência entre carteiras (P2P)",
     * tags={"Wallet"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"amount", "wallet_id_source", "wallet_id_destination"},
     * @OA\Property(property="wallet_id_source", type="string", format="uuid", description="Quem paga", example="source-uuid-123"),
     * @OA\Property(property="wallet_id_destination", type="string", format="uuid", description="Quem recebe", example="dest-uuid-456"),
     * @OA\Property(property="amount", type="number", format="float", example=25.00)
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Transferência realizada com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="id", type="integer", example=10),
     * @OA\Property(property="amount", type="number", example=25.00),
     * @OA\Property(property="type", type="string", example="transfer"),
     * @OA\Property(property="created_at", type="string", format="date-time")
     * )
     * ),
     * @OA\Response(response=400, description="Erro de negócio (Saldo insuficiente, Lojista, etc)"),
     * @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function transfer(Request $request, TransferMoneyHandler $handler)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'wallet_id_source' => 'required|uuid|exists:wallets,id',
            'wallet_id_destination' => 'required|uuid|exists:wallets,id|different:wallet_id_source',
        ]);

        $senderWallet = $request->user()->wallet;
        if ($senderWallet->id === $data['wallet_id_destination']) {
            abort(422, 'Você não pode transferir para si mesmo.');
        }
        if($senderWallet->id !== $data['wallet_id_source']){
            abort(422, 'Você só pode transferir da sua própria carteira.');
        }

        $command = new TransferMoneyCommand(
            walletIdpayer: $senderWallet->id,
            walletIdpayee: $data['wallet_id_destination'],
            amount: $data['amount'],
        );

        $transaction = $handler->handle($command);
        return response()->json($transaction, 201); // 201 Created
    }

    /**
     * @OA\Get(
     * path="/api/wallet/{id}/statement",
     * summary="Consulta extrato de transações",
     * tags={"Wallet"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID da Carteira",
     * required=true,
     * @OA\Schema(type="string", format="uuid")
     * ),
     * @OA\Parameter(
     * name="start_date",
     * in="query",
     * description="Data inicial (YYYY-MM-DD)",
     * required=true,
     * @OA\Schema(type="string", format="date")
     * ),
     * @OA\Parameter(
     * name="end_date",
     * in="query",
     * description="Data final (YYYY-MM-DD)",
     * required=true,
     * @OA\Schema(type="string", format="date")
     * ),
     * @OA\Response(
     * response=200,
     * description="Lista de transações",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(
     * @OA\Property(property="id", type="integer"),
     * @OA\Property(property="amount", type="number"),
     * @OA\Property(property="type", type="string", example="transfer"),
     * @OA\Property(property="description", type="string"),
     * @OA\Property(property="created_at", type="string", format="date-time")
     * )
     * )
     * )
     * )
     */
    public function statement(string $id, Request $request, GetWalletStatementHandler $handler)
    {
        $data = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $senderWallet = $request->user()->wallet->id;
        if ($senderWallet !== $id) {
            abort(422, 'Você não pode visualizar outras extratos.');
        }

        $query = new GetWalletStatementQuery(
            walletId: $id,
            startDate: $data['start_date'],
            endDate: $data['end_date'],
        );

        $statement = $handler->handle($query);
        return response()->json($statement);
    }

    /**
     * @OA\Get(
     * path="/api/wallet/{id}/balance",
     * summary="Consulta saldo atual",
     * tags={"Wallet"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="string", format="uuid")
     * ),
     * @OA\Response(
     * response=200,
     * description="Saldo retornado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="balance", type="number", example=1500.00)
     * )
     * )
     * )
     */
    public function balance(string $id, Request $request, GetWalletBalanceHandler $handler)
    {
        // Removido o Request $request pois não é usado aqui
        $balance = $handler->handle(new GetWalletBalanceQuery($id));
        $senderWallet = $request->user()->wallet->id;
        if ($senderWallet !== $id) {
            abort(422, 'Você não pode visualizar outros balanços.');
        }
        return response()->json(['balance' => $balance]);
    }

    /**
     * @OA\Get(
     * path="/api/wallet/{id}",
     * summary="Consulta detalhes da carteira",
     * tags={"Wallet"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID do usuario",
     * required=true,
     * @OA\Schema(type="string", format="uuid")
     * ),
     * @OA\Response(
     * response=200,
     * description="Carteira encontrada com sucesso",
     * @OA\JsonContent(
     * required={"id", "user_id", "balance"},
     * @OA\Property(property="id", type="string", format="uuid", example="019b2a70-24e1-73b7-a8e3-671714e36872"),
     * @OA\Property(property="user_id", type="string", format="uuid", example="019b2a66-104f-7119-9fd4-0c0708bf7eb1"),
     * @OA\Property(property="balance", type="number", format="float", example=1500.00),
     * @OA\Property(property="created_at", type="string", format="date-time"),
     * @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     * ),
     * @OA\Response(response=404, description="Carteira não encontrada")
     * )
     */
    public function getWallet(string $id, Request $request, WallaetHandler $handler)
    {
        // Certifique-se de criar/renomear a Query e o Handler corretamente
        $query = new WallaetQuery($id);

        $senderWallet = $request->user()->id;
        if ($senderWallet !== $id) {
            abort(422, 'Você não pode visualizar outras carteiras.');
        }

        $wallet = $handler->handle($query);

        return response()->json($wallet);
    }
}
