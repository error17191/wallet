<?php

namespace App\Enums;

enum TransactionType: string
{
    case TOP_UP = 'top-up';
    case CHARGE = 'charge';

    public static function values(): array
    {
        return [
            self::TOP_UP->value,
            self::CHARGE->value,
        ];
    }
}
