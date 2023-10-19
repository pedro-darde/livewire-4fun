<?php

namespace App\DTO;

class DynamicTableDTO
{

    public function __construct(
        public readonly string          $columnDescription,
        public readonly string          $columnName,
        public readonly mixed           $columnRules,
        public readonly bool            $allowEdit = true,
        public readonly array|\stdClass $extraProps = []
    )
    {
    }

    public function getColumnDescription(): string
    {
        return $this->columnDescription;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function getColumnRules(): string|callable
    {
        return $this->columnRules;
    }

    public function isAllowEdit(): bool
    {
        return $this->allowEdit;
    }

    public function getExtraProps(): array
    {
        return $this->extraProps;
    }

    static function generateByArrayDefinition(array $definitions): array
    {
        return array_map(function ($item) {
            return new DynamicTableDTO(
                $item['columnDescription'],
                $item['columnName'],
                $item['columnRules'],
                $item['allowEdit'],
                @$item['extraProps'] ?? []
            );
        }, $definitions);
    }
}
