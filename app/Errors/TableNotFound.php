<?php

namespace App\Errors;

class TableNotFound extends \Exception
{
    public function __construct(string $table)
    {
        parent::__construct("Table $table has not been found.");
    }
}
