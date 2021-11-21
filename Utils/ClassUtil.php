<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Utils;

/**
 * @internal
 */
final class ClassUtil
{
    public static function isNotBuiltinAndExists(string $class): bool
    {
        return class_exists($class) && !empty((new \ReflectionClass($class))->getNamespaceName());
    }

    public static function getShortName(string $class): string
    {
        try {
            return (new \ReflectionClass($class))->getShortName();
        } catch (\ReflectionException) {
            return $class;
        }
    }

    public static function isNameContains(string $class, string ...$haystack): bool
    {
        return \count(array_filter($haystack, static fn (string $value) => StringUtil::contains(self::getShortName($class), $value))) > 0;
    }
}
