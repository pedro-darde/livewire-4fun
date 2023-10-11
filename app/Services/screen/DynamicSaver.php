<?php

namespace App\Services\screen;

use App\Models\DynamicModel;
use App\Models\Screen;
use Illuminate\Support\Facades\DB;

class DynamicSaver
{
    static function save(string $table, array $data)
    {
        /** @var Screen $screen */
        $screen = Screen::where(['table' => $table])->first();
        $operation = empty($data[$screen->pk_name]) ? 'insert' : 'update';

        if ($operation == 'insert') {
            unset($data[$screen->pk_name]);
        }
        DynamicModel::saveDynamic($table, $data, $operation);
    }
}
