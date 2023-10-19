<?php

namespace App\Enums;

enum RecurrenceType: int
{
    case NONE = 0;
    case WEEKLY =1;
    case BIWEEKLY = 2;
    case MONTHLY = 3;

    static function none(): int
    {
        return self::NONE->value;
    }

    static function weekly(): int
    {
        return self::WEEKLY->value;
    }

    static function biweekly(): int
    {
        return self::BIWEEKLY->value;
    }

    static function monthly(): int
    {
        return self::MONTHLY->value;
    }
}
