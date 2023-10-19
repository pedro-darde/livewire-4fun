<?php

namespace App\Services\screen;

use App\Models\Screen;
use App\Models\ScreenFields;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ScreenCreatorProcessor extends BaseScreenService
{
    private Screen $createdScreen;

    public function process(array $inputPost)
    {
        $this->runValidationBeforeProcess($inputPost);

        try {
            $screen = new Screen();
            $screen->name = Str::ucfirst($inputPost['table']);
            $screen->table = $inputPost['table'];
            $screen->title = $inputPost['title'];
            $screen->icon = $inputPost['icon'];
            $screen->description = $inputPost['description'];
            $screen->url = $inputPost['url'];
            $screen->pk_name = Str::snake($inputPost['pk_name']);
            $screen->description_column = $inputPost['description_column'];
            $screen->save();

            $this->createdScreen = $screen;

            $fieldsCollection = [
                [
                    'config' => json_encode([
                        "name" => $screen->pk_name,
                        "label" => "Identifier",
                        "type" => "",
                        "mask" => "",
                        "placeholder" => "",
                        "options" => [],
                        "default" => "",
                        "description" => "",
                        "required" => false,
                        "rules" => [],
                        "disabled" => true,
                        "visible" => false,
                        "searchUrl" => "",
                        "multiple" => false,
                        "useAjaxToLoadOptions" => false,
                        "useIndex" => false
                    ]),
                    'screen_id' => $screen->id
                ]
            ];
            if (count($inputPost['fields'])) {
                $fieldsCollection = array_merge($fieldsCollection, array_map(function ($field) use ($screen) {
                    return [
                        'config' => json_encode($field['config']),
                        'screen_id' => $screen->id
                    ];
                }, $inputPost['fields']) ?? []);
            }

            ScreenFields::insert($fieldsCollection);
            $this->createDynamicTable($screen, $inputPost['fields']);
        } catch (\Exception $ex) {
            $this->rollback();
            throw $ex;
        }
    }

    private function createDynamicTable(Screen $screen, array $fields)
    {
        $sql = "CREATE TABLE `{$screen->table}` (
            {$screen->pk_name} SERIAL PRIMARY KEY,
        ";

        if (count($fields)) {
            $sql .= $this->getCreateFieldsString($fields);
        }

        $sql = rtrim($sql, ", ");

        $sql .= ")";

        $this->sqlCreate = $sql;

        DB::statement($sql);
    }

    private function getCreateFieldsString($fields)
    {
        $sql = '';

        $relations = [];
        foreach ($fields as $field) {
            $config = $field['config'];
            $fieldName = Str::snake($config['name']);
            $sql .= ' ' . $fieldName . ' ' . static::getSqlType($config['type']) . ' ';
            if ($config['required']) {
                $sql .= ' NOT NULL ';
            }

//            if ($config['default']) {
//                $defaultValue = static::parseDefaultValueByType($config['type'], $config['default']);
//                $sql .= " DEFAULT {$defaultValue}";
//            }

            if ($config['relations']) {
                $relationWithValue = collect($config['relations'])->filter(function ($relation) {
                    return !empty($relation['screen']) && !empty($relation['field']);
                })->all();

                if ($relationWithValue) {
                    $relations[] = [
                        'field' => $fieldName,
                        'references' => $relationWithValue
                    ];
                }
            }
            if ($config['useIndex']) {
                $sql .= $this->getCreateIndexField($config);
            }

            $sql .= ', ';
        }


        if (count($relations)) {
            $sql .= $this->getCreateFkString($relations);
        }

        return $sql;
    }

    private function getCreateFkString(array $relations): string
    {
        $fksString = [];
        foreach ($relations as $relationDefinition) {
            [
                'field' => $field,
                'references' => $references
            ] = $relationDefinition;

            foreach ($references as $reference) {
                $referencedTable = $reference['screen']['table'];
                $fieldConfig = json_decode($reference['field']['config']);
                $referencedField = Str::snake($fieldConfig->name);
                $fksString[] = "FOREIGN KEY ($field) REFERENCES $referencedTable($referencedField)";
            }
        }

        return implode(", ", $fksString);
    }

    private function getCreateIndexField($field): string
    {
        return ' CREATE INDEX ' . $field['name'] . '_idx ON ' . $field['name'] . ';';
    }

    private function rollback()
    {
        if ($this->createdScreen->id) {
            $this->createdScreen->fields()->delete();
            $this->createdScreen->delete();
            DB::statement("DROP TABLE IF EXISTS  `{$this->createdScreen->table}`;");
            dd($this->sqlCreate);
        }
    }
}
