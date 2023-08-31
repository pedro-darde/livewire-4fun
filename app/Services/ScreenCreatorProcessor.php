<?php

namespace App\Services;

use App\Models\Screen;
use App\Models\ScreenFields;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ScreenCreatorProcessor
{
    private static array $ALLOWED_FIELD_TYPES_MAP = [
        'numeric' => 'float',
        'text' => 'string',
        'textarea' => 'text',
        'select' => 'string',
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
    public function process(array $inputPost)
    {
        DB::beginTransaction();
        try {

            $screen = new Screen();
            $screen->name = $inputPost['name'];
            $screen->table = $inputPost['table'];
            $screen->title = $inputPost['title'];
            $screen->icon = $inputPost['icon'];
            $screen->description = $inputPost['description'];
            $screen->url = $inputPost['url'];
            $screen->pk_name = $inputPost['pk_name'];
            $screen->save();

            $fieldsCollection = array_map(function ($field)  use($screen) {
                $fieldModel = new ScreenFields();
                $fieldModel->config = $field['config'];
                $fieldModel->screen_id = $screen->id;
                return $fieldModel;
            }, $inputPost['fields']);

            $screen->fields()->createMany($fieldsCollection);

            $this->createDynamicTable($screen, $inputPost['fields']);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
        }
    }

    private function createDynamicTable(Screen $screen, array $fields)
    {
        $sql = "CREATE TABLE {$screen->table} (
            {$screen->pk_name} SERIAL PRIMARY KEY,
        ";

        $sql .= $this->getCreateFieldsString($fields);
    }

    private function getCreateFieldsString($fields)
    {
        $sql = '';
        foreach ($fields as $field) {
            $config = $field['config'];
            $sql .= ' ' . $config['name'] . ' ' . $this->getSqlType($config['type']) . ',';

            if ($config['required']) {
                $sql .= ' NOT NULL,';
            }

            if ($config['default']) {
                $sql .= ' DEFAULT ' . $config['default'] . ',';
            }

            if ($config['references']) {
              $sql .= $this->getCreateFkString($config['references']);
            }

            if ($config['useIndex']) {
                $sql.= $this->getCreateIndexField($config);
            }
        }
        return $sql;
    }

    private function getCreateFkString(array $references) : string
    {
        $sql =  '';

        foreach($references as $reference) {
            [
                'table' => $table,
                'field' => $field,
            ] = $reference;

            $sql .= ' CONSTRAINT ' . $field['name'] . '_fk' . ' FOREIGN KEY (' . $field['name'] . ') REFERENCES ' . $table . '(' . $field . '), ';
        }

        return $sql;
    }

    private function getCreateIndexField($field): string
    {
        return ' CREATE INDEX ' . $field['name'] . '_idx ON ' . $field['name'] . ';';
    }

    private function getSqlType($type)
    {
        return self::$ALLOWED_FIELD_TYPES_MAP[$type];
    }

}
