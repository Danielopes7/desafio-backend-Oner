<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING  = 'pending';
    case APPROVED = 'approved';
    case REVERSED = 'reversed';
    case ERROR    = 'error';
}

