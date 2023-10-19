<?php

namespace App\Traits;

use App\DTO\DynamicTableDTO;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;


/**
 * @property array $fillableWithDefinitions
 * @property bool $useRecordActions
 */
trait HasDynamicTable
{
    static function getDtoColumnDefinitions(): array
    {
        $arrayDefinition = [];

        if (empty(static::$fillableWithDefinitions)) {
            $model = new static;
            $columns = array_filter(Schema::getColumnListing($model->getTable()), function ($item) use ($model) {
                return !in_array($item, $model->getHidden());
            });
            dd($columns);
        }

        foreach (static::$fillableWithDefinitions as $columnName => $definition) {
            $arrayDefinition[] = [
                'columnName' => $columnName,
                'columnDescription' => $definition['columnDescription'],
                'columnRules' => $definition['columnRules'],
                'allowEdit' => $definition['allowEdit'] ?? true
            ];
        }
        return DynamicTableDTO::generateByArrayDefinition($arrayDefinition);
    }

    static function getOnlyRules() {
        $rules = [];
        foreach(static::getDtoColumnDefinitions() as $item) {
            if (empty($item->getColumnRules())) continue;
            $rules[$item->columnName] = $item->getColumnRules();
        }
        return $rules;
    }

    public function getTDAttribute(): string
    {
        $strReturn = "";
//        foreach(static::$fillableWithDefinitions as $columnName => $_) {
//            if (in_array($columnName, $this->getHidden())) continue;
//            $strReturn .= "<td class='p-4'> {$this->{$columnName}} </td>";
//        }
//
//        if (static::$useRecordActions) {
//            $strReturn .= <<<HTML
//            <td class="p-4">
//                  <a href="#" class="p-2">
//                    <i class="fas fa-edit">
//                    </i>
//                  </a>
//                   <a href="#" class="p-2" >
//                    <i class="fas fa-remove"> </i>
//                </a>
//            </td>
//HTML;
//        }
        return $strReturn;
    }

    public function scopeFilterBySearchString($query,string $searchString = '', $orderBy = [], $orderDirection = 'asc')
    {
        if (empty($orderBy)) {
            $orderBy = Arr::map(static::getDtoColumnDefinitions(), fn ($item) => $item->getColumnName());
        }
        $q = $query->where(function ($query) use ($searchString) {
           $count = 0;
           foreach(static::$fillableWithDefinitions as $columnName => $_) {
               if (in_array($columnName, $this->getHidden())) continue;
               $method = $count === 0 ? "where" : "orWhere";
               $count++;

               $operation = $this->extractLikeOperatorByDatabaseType();
               $query->{$method}($columnName, $operation, "$searchString%");
           }
        });
        foreach ($orderBy as $order) {
            $q->orderBy($order, $orderDirection);
        }
        return $q;
    }

    private function extractLikeOperatorByDatabaseType()
    {
        $dbType = env("DB_TYPE") ?? "mysql";

        switch($dbType) {
            case "mysql":
                return "LIKE";
            default:
                return "ILIKE";
        }
    }
}
