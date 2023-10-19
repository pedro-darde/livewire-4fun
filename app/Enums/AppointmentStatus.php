<?php

namespace App\Enums;

/**
 * @method static string PENDING()
 * @method static string CONFIRMED()
 * @method static string CANCELED()
 * @method static string FINISHED()
 */
enum AppointmentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELED = 'canceled';

    case FINISHED = 'finished';
    public static function __callStatic(string $name, $arguments): string {
        return self::from(strtolower($name))->value;
    }
}
