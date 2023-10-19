<?php

namespace App\Utils;

class Parser
{
    static function toBoolSql(mixed $value) {
        return ($value === 'true'
            || $value === true
            || $value == 't'
            || $value == '1'
            || $value == 1
            || $value == 'on'
            || $value == 'yes'
            || $value == 'y'
            || $value == 'checked'
            || $value == 'selected'
            || $value == 'enabled'
            || $value == 'active'
        ) ? 'true' : 'false';
    }
}
