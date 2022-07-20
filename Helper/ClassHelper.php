<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use Fp\Collections\ArrayList;

/**
 * @internal
 */
final class ClassHelper
{
    public static function isNotBuiltinAndExists(string $className): bool
    {
        return class_exists($className) && !empty((new \ReflectionClass($className))->getNamespaceName()) && !self::isEnum($className);
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

    public static function getPropertyMapping(string $className): array
    {
        try {
            $properties = (new \ReflectionClass($className))->getProperties();
        } catch (\ReflectionException) {
            $properties = [];
        }

        return ArrayList::collect($properties)
            ->map(static fn (\ReflectionProperty $p): array => match (true) {
                ConstraintsHelper::hasArrayTypeAttribute($p) => [SerializerHelper::getSerializedNameAttribute($p)->getSerializedName() => [ConstraintsHelper::getArrayTypeAttribute($p)->typeName]],
                default => [SerializerHelper::getSerializedNameAttribute($p)->getSerializedName() => $p->getType()?->/** @scrutinizer ignore-call */ getName()],
            })
            ->reduce(static fn (array $prev, array $next) => [...$prev, ...$next])
            ->getOrElse([])
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
}
