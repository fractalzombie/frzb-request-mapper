<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Utils;

/**
 * @internal
 */
final class ObjectUtil
{
    /** @param class-string $class */
    public static function isArrayHasAllPropertiesFromClass(array $array, string $class): bool
    {
        try {
            $rClass = new \ReflectionClass($class);
        } catch (\ReflectionException) {
            return false;
        }

        foreach ($rClass->getProperties() as $property) {
            $propertyValue = $array[$property->getName()] ?? $array[StringUtil::toSnakeCase($property->getName())] ?? null;

            if (!$propertyValue) {
                return false;
            }
        }

        return true;
    }
}
