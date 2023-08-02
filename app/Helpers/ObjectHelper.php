<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class ObjectHelper
{
    static function pickFromModel(array| string $pickableKeys, Model $model): \stdClass
    {
        if (is_string($pickableKeys)) $pickableKeys = [$pickableKeys];
        $returnStd = new \stdClass();
        foreach($pickableKeys as $key) {
            if (property_exists($model, $key)) {
                $returnStd->{$key} = $model->{$key};
            }
        }
        return $returnStd;
    }
}
