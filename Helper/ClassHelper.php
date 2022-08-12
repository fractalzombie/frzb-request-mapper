<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use Fp\Collections\ArrayList;
use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class ClassHelper
{
    private function __construct()
    {
    }

    public static function isNotBuiltinAndExists(string $className): bool
    {
        return (class_exists($className) || interface_exists($className))
            && !empty((new \ReflectionClass($className))->getNamespaceName())
            && !self::isEnum($className)
        ;
    }

    public static function isEnum(string $className): bool
    {
        return enum_exists($className);
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
        return ArrayList::collect($haystack)
            ->filter(static fn (string $value): bool => StringHelper::contains(self::getShortName($className), $value))
            ->isNonEmpty()
        ;
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

    /**
     * @template T
     *
     * @param class-string<T> $attributeClass
     *
     * @return null|T
     */
    public static function getAttribute(string|object $target, string $attributeClass): ?object
    {
        return ArrayList::collect(self::getAttributes($target, $attributeClass))
            ->firstElement()
            ->get()
        ;
    }

    /**
     * @template T
     *
     * @param class-string<T> $attributeClass
     *
     * @return array<T>
     */
    public static function getAttributes(string|object $target, string $attributeClass): array
    {
        try {
            $attributes = (new \ReflectionClass($target))->getAttributes($attributeClass);
        } catch (\ReflectionException) {
            $attributes = [];
        }

        return ArrayList::collect($attributes)
            ->map(static fn (\ReflectionAttribute $a) => $a->newInstance())
            ->toArray()
        ;
    }
}
