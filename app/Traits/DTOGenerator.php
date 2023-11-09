<?php

namespace App\Traits;

trait DTOGenerator
{
    static function createNewInstance(array $requestData): self {
        $reflection = new \ReflectionClass(static::class);
        $instance = $reflection->newInstanceWithoutConstructor();

        $createdProperties = [];
        foreach($requestData as $key => $value) {
            $createdProperties[] = $key;
            $instance->{$key} = $value;
        }

        $allProperties = $reflection->getProperties();

        $nonCreatedProperties = array_filter($allProperties, function($property) use ($createdProperties) {
            return !in_array($property->getName(), $createdProperties);
        });

        foreach($nonCreatedProperties as $property) {
            $instance->{$property->getName()} = null;
        }

        return $instance;
    }
}
