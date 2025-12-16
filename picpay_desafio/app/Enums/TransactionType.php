<?php

namespace App\Enums;

enum TransactionType: string
{
    case SENDING = 'sending';
    case RECEIVING = 'receiving';
}
