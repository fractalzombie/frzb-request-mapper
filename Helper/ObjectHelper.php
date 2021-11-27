<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

/**
 * @internal
 */
final class ObjectHelper
{
    public static function isArrayHasAllPropertiesFromClass(array $array, string $class): bool
    {
        try {
            $rClass = new \ReflectionClass($class);
        } catch (\ReflectionException) {
            return false;
        }

        foreach ($rClass->getProperties() as $property) {
            $propertyValue = $array[$property->getName()] ?? $array[StringHelper::toSnakeCase($property->getName())] ?? null;

            if (!$propertyValue) {
                return false;
            }
        }

        return true;
    }
}
