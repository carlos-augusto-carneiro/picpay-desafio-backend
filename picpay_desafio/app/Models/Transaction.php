<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TransactionType;

class Transaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'amount',
        'type',
        'wallet_id_destination',
        'description',
    ];
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'type'=> TransactionType::class,
        ];
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function walletDestination()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id_destination');
    }
}
