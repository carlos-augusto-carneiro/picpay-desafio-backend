<?php

namespace App\CQRS\Wallet\Handlers;

use App\CQRS\Wallet\Queries\GetWalletStatementQuery;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class GetWalletStatementHandler
{
    public function handle(GetWalletStatementQuery $query): LengthAwarePaginator
    {

        $startDate = Carbon::parse($query->startDate)->startOfDay();
        $endDate   = Carbon::parse($query->endDate)->endOfDay();

        return Transaction::query()
            ->where(function ($q) use ($query) {
                $q->where('wallet_id', $query->walletId)
                  ->orWhere('wallet_id_destination', $query->walletId);
            })
            ->whereBetween('created_at', [$startDate, $endDate])

            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
}
