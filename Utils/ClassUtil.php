<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Utils;

/**
 * @internal
 */
final class ClassUtil
{
    public static function isNotBuiltinAndExists(string $className): bool
    {
        return class_exists($className) && !empty((new \ReflectionClass($className))->getNamespaceName());
    }

    /**
     * @param class-string $className
     *
     * @return class-string|string
     */
    public static function getShortName(string $className): string
    {
        try {
            return (new \ReflectionClass($className))->getShortName();
        } catch (\ReflectionException) {
            return $className;
        }
    }

    /** @param class-string $className */
    public static function isNameContains(string $className, string ...$haystack): bool
    {
        $filter = static fn (string $value): bool => StringUtil::contains(self::getShortName($className), $value);

        return \count(array_filter($haystack, $filter)) > 0;
    }

    /** @param class-string $className */
    public static function getPropertyMapping(string $className): array
    {
        try {
            $properties = (new \ReflectionClass($className))->getProperties();
        } catch (\ReflectionException) {
            $properties = [];
        }

        $map = static fn (\ReflectionProperty $p): array => [
            SerializerUtil::getSerializedNameAttribute($p)->getSerializedName() => $p->getType()?->getName(),
        ];

        return array_merge(...array_map($map, $properties));
    }
}
