<?php

namespace App\Traits;
interface
WithDynamicTable
{
    static function getDtoColumnDefinitions(): array;

    public function getTDAttribute(): string;

    public function scopeFilterBySearchString($query,string $searchString);
}
