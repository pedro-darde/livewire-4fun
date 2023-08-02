<?php

namespace App\DTO;

class DynamicTableDTO
{
    public string $columnDescription;
    public string $columnName;


    public function __construct(string $columnDescription, string $columnName )
    {
        $this->columnDescription = $columnDescription;
        $this->columnName = $columnName;
    }

    public function getColumnDescription(): string
    {
        return $this->columnDescription;
    }
    public function getColumnName(): string
    {
        return $this->columnName;
    }

    static function generateByArrayDefinition(array $definitions): array
    {
        return array_map(function ($item)  {
            return new DynamicTableDTO($item['columnDescription'], $item['columnName']);
        }, $definitions);
    }
}
