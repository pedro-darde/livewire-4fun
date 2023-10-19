<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Icon extends Model
{
    use HasFactory;

    static $icons;
    static function getAll(): \Illuminate\Support\Collection {
        if (!isset($icons)) {
            static::$icons = collect(
                json_decode(
                    file_get_contents(base_path('resources/json/icons.json')),
                    true
                )
            );
        }
        return static::$icons;
    }
}
