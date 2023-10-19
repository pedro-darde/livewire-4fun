<?php

namespace App\Services\screen;

use App\Errors\AlreadyExistsException;
use App\Models\Screen;
use App\Utils\Parser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class BaseScreenService
{
    protected static array $ALLOWED_FIELD_TYPES_MAP = [
        'numeric' => 'float',
        'text' => 'varchar(255)',
        'textarea' => 'varchar(1500)',
        'select' => 'bigint unsigned',
        'checkbox' => 'boolean',
        'radio' => 'string',
        'date' => 'date',
        'time' => 'time',
        'datetime' => 'datetime',
        'file' => '',
        'image' => '',
        'password' => 'varchar',
        'email' => 'varchar',
        'url' => 'varchar',
        'color' => 'varchar',
        'range',
        'hidden',
    ];

    protected string $sqlCreate = '';


    static protected function getSqlType($type)
    {
        return self::$ALLOWED_FIELD_TYPES_MAP[$type];
    }

    static function parseDefaultValueByType(string $type, string $defaultValue)
    {
        switch (static::getSqlType($type)) {
            case "boolean" :
                return  Parser::toBoolSql($defaultValue);
            default:
                return "'{$defaultValue}'";
        }
    }
    protected function runValidationBeforeProcess(array $input)
    {
        if ($this->tableExists($input['table'])) {
            throw new AlreadyExistsException("Table {$input['table']} already exists.");
        }
    }
    private function tableExists($tableName)
    {
        return Schema::hasTable($tableName);
    }

    static function getExistingScreens(): Collection
    {
        return Screen::with('fields')->get();
    }
}
