<?php

namespace App\Traits;

use App\DTO\DynamicTableDTO;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;


/**
 * @property array $fillableWithDefinitions
 * @property bool $useRecordActions
 */
trait HasDynamicTable
{
    static function getDtoColumnDefinitions()
    {
        $arrayDefinition = [];
        foreach (static::$fillableWithDefinitions as $columnName => $definition) {
            $arrayDefinition[] = [
                'columnName' => $columnName,
                'columnDescription' => $definition
            ];
        }
        return DynamicTableDTO::generateByArrayDefinition($arrayDefinition);
    }

    public function getTD()
    {
        $strReturn = "";
        foreach(static::$fillableWithDefinitions as $columnName => $_) {
            $strReturn .= "<td class='px-6 py-4'> {$this->{$columnName}} </td>";
        }

        if (static::$useRecordActions) {
            $strReturn .= <<<HTML
            <td class="px-6 py-4">
                  <a href="#" class="p-2" >
                    <i class="fas fa-edit">
                    </i>
                  </a>
                   <a href="#" class="p-2" onclick="showToastDelete({$this->id})">
                    <i class="fas fa-remove"> </i>
                </a>
            </td>
HTML;
        }
        return $strReturn;
    }

    static function filterBySearchString(string $searchString): \Illuminate\Support\Collection
    {
       $table = (new static)->getTable();
       return DB::table($table)->where(function (Builder $query) use ($searchString) {
           $count = 0;
           foreach(static::$fillableWithDefinitions as $columnName => $_) {
               $method = $count === 0 ? "where" : "orWhere";
               $count++;
               $query->{$method}($columnName, "ILIKE", "$searchString%");
           }
       })->get();
    }
}
