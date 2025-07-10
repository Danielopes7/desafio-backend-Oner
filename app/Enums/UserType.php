<?php

namespace App\Enums;

enum UserType: string
{
    case CUSTOMER = 'customer';
    case SHOPKEEPER = 'shopkeeper';
}
