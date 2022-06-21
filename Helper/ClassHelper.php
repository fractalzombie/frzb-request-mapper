<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

/**
 * @internal
 */
final class ClassHelper
{
    public static function isNotBuiltinAndExists(string $className): bool
    {
        return class_exists($className) && !empty((new \ReflectionClass($className))->getNamespaceName());
    }

    public static function getShortName(string $className): string
    {
        try {
            return (new \ReflectionClass($className))->getShortName();
        } catch (\ReflectionException) {
            return $className;
        }
    }

    public static function isNameContains(string $className, string ...$haystack): bool
    {
        $filter = static fn (string $value): bool => StringHelper::contains(self::getShortName($className), $value);

        return \count(array_filter($haystack, $filter)) > 0;
    }

    public static function getPropertyMapping(string $className): array
    {
        try {
            $properties = (new \ReflectionClass($className))->getProperties();
        } catch (\ReflectionException) {
            $properties = [];
        }

        $map = static fn (\ReflectionProperty $p): array => match (true) {
            ConstraintsHelper::hasArrayTypeAttribute($p) => [SerializerHelper::getSerializedNameAttribute($p)->getSerializedName() => [ConstraintsHelper::getArrayTypeAttribute($p)->typeName]],
            default => [SerializerHelper::getSerializedNameAttribute($p)->getSerializedName() => $p->getType()?->/** @scrutinizer ignore-call */ getName()],
        };

        return array_merge(...array_map($map, $properties));
    }

    /** @return \ReflectionParameter[] */
    public static function getMethodParameters(string $className, string $classMethod): array
    {
        try {
            return (new \ReflectionMethod($className, $classMethod))->getParameters();
        } catch (\ReflectionException) {
            return [];
        }
    }
}
