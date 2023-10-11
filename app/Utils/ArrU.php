<?php

namespace App\Utils;

class ArrU
{
    static function every(array $array, callable $callback)
    {
        if (empty($array)) return false;
        foreach ($array as $key => $value) {
            if (!$callback($value, $key)) {
                return false;
            }
        }
        return true;
    }
}
