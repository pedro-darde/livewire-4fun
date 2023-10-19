<?php

namespace App\Models;

use App\DTO\DynamicTableDTO;
use App\Errors\TableNotFound;
use App\Utils\ArrU;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DynamicModel extends Model
{

    static array $screens = [];
    static array $itemsSelect = [];

    static function getColumnDefinitions(string $table)
    {

        if (!isset(static::$screens[$table])) {
            static::$screens[$table] = Screen::where('table', $table)->first();
        }

        $screen = static::$screens[$table];

        if ($screen) {
            $definitions = $screen->fields->map(function ($field) use ($screen) {
                return [
                    'columnName' => $field->columnName,
                    'type' => $field->type,
                    'columnRules' => $field->rules,
                    'columnDescription' => $field->label ?? '',
                    'allowEdit' => $field->columnName != $screen->pk_name,
                    'extraProps' => $field->config_parsed,
                ];
            });


            $receivedReferencesFields = $screen->receivedReferences();

            foreach ($receivedReferencesFields as $field) {
                $definitions->push([
                    'columnName' => $field->screen->name,
                    'type' => $field->type,
                    'columnRules' => $field->rules,
                    'columnDescription' => $field->screen->name,
                    'allowEdit' => false,
                    'extraProps' => $field->config_parsed,
                ]);
            }

            return DynamicTableDTO::generateByArrayDefinition($definitions->toArray());
        }

        return [];
    }

    /**
     * @throws TableNotFound
     */
    static function getItemsToList(
        string $table,
        string $optionText,
        string $optionValue
    ): mixed
    {
        if (!isset(static::$itemsSelect[$table])) {
            if (!Schema::hasTable($table)) throw new TableNotFound($table);
            static::$itemsSelect[$table] = DB::table($table)->get()->map(function ($item) use ($optionText, $optionValue) {
                return [
                    'value' => $item->{$optionValue},
                    'text' => $item->{$optionText}
                ];
            });
        }

        return static::$itemsSelect[$table];
    }

    public static function getRegisters(string $table, Screen $screen)
    {
        $select = static::buildScreenSelectString($screen);


        $registers = DB::table($table)
            ->selectRaw($select)
            ->from($table, $screen->name)
            ->paginate(10);

//        dd($registers->items());
        return $registers;
    }

    public static function saveDynamic(string $table, array $data, string $operation): void
    {
        if (!in_array($operation, ['insert', 'update'])) {
            throw new \Exception('Operation not allowed');
        }

        DB::table($table)->{$operation}(
            $data
        );
    }

    private static function buildScreenSelectString(Screen $screen)
    {
        $tableAlias = "`$screen->name`";

        $selectCols = [];


        foreach ($screen->fields as $field) {
            $hasRelation = ArrU::every($field->configparsed->relations ?? [], function ($relation) {
                return isset($relation->field->id) && isset($relation->screen->id);
            });

            if ($hasRelation) {
                [$relationConfig] = $field->configparsed->relations;
                $optionValue = $field->configparsed->optionValue;
                $optionText = $field->configparsed->optionText;
                $selectCols[] = "(SELECT {$relationConfig->screen->table}.{$optionText} FROM {$relationConfig->screen->table} WHERE {$relationConfig->screen->table}.{$optionValue} = $tableAlias.{$field->columnName} LIMIT 1)  as {$field->column_name}";

            } else {
                $selectCols[] = "$tableAlias.$field->columnName";
            }

        }

        $receivedReferences = $screen->receivedReferences();
        foreach ($receivedReferences as $field) {
            $selectCols[] = "(SELECT GROUP_CONCAT(`{$field->screen->table}`.{$field->screen->description_column} SEPARATOR ',') FROM `{$field->screen->table}` WHERE `{$field->screen->table}`.{$field->columnName} =  {$tableAlias}.{$screen->pk_name} GROUP BY `{$field->screen->table}`.{$field->columnName})  as `{$field->screen->name}`";
        }

        return implode(", ", $selectCols);
    }
}
