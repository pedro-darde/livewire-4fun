<?php

namespace App\Constants;

enum Order: string
{
  case ASCENDING = 'asc';
  case DESCENDING = 'desc';

  static function getAsList() {
      return [
          [
            'name' => self::ASCENDING->name,
            'value' => self::ASCENDING->value,
          ],
          [
              'name' => self::DESCENDING->name,
              'value' => self::DESCENDING->value,
          ],
      ];
  }
}
