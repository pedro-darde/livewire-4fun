<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DateTime implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): ?string
    {
        return $value ? date('d/m/Y H:i', strtotime($value)) : null;
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value ? date('Y-m-d H:i', strtotime($value)) : null;
    }
}
