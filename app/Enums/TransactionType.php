<?php

namespace App\Enums;

enum TransactionType: string
{
    case TRANSFER = 'transfer';
    case WITHDRAW = 'withdraw';
    case DEPOSIT = 'deposit';
    case REFUND = 'refund';
}
