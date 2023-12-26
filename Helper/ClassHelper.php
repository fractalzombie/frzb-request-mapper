<?php

declare(strict_types=1);

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *
 * Copyright (c) 2023 Mykhailo Shtanko fractalzombie@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE.MD
 * file that was distributed with this source code.
 */

namespace FRZB\Component\RequestMapper\Helper;

use Fp\Collections\ArrayList;
use FRZB\Component\RequestMapper\Exception\HelperException;
use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class ClassHelper
{
    private function __construct() {}

    public static function isNotBuiltinAndExists(string $className): bool
    {
        return (class_exists($className) || interface_exists($className))
            && !empty((new \ReflectionClass($className))->getNamespaceName())
            && !self::isEnum($className);
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
            ->first(static fn (string $value): bool => StringHelper::contains(self::getShortName($className), $value))
            ->isSome()
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

    public static function getMethodParameter(string $className, string $classMethod, string $parameterName): \ReflectionParameter
    {
        return ArrayList::collect(self::getMethodParameters($className, $classMethod))
            ->first(static fn (\ReflectionParameter $property) => $property->getName() === $parameterName)
            ->getOrElse(fn () => throw HelperException::noMethodParameter($className, $classMethod, $parameterName))
        ;
    }

    /** @return \ReflectionProperty[] */
    public static function getProperties(string $className): array
    {
        try {
            return (new \ReflectionClass($className))->getProperties();
        } catch (\ReflectionException) {
            return [];
        }
    }

    public static function getProperty(string $className, string $propertyName): \ReflectionProperty
    {
        return ArrayList::collect(self::getProperties($className))
            ->first(static fn (\ReflectionProperty $property) => $property->getName() === $propertyName)
            ->getOrElse(fn () => throw HelperException::noClassProperty($className, $propertyName))
        ;
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
    public static function getAttribute(object|string $target, string $attributeClass): ?object
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
    public static function getAttributes(object|string $target, string $attributeClass): array
    {
        try {
            $attributes = (new \ReflectionClass($target))->getAttributes($attributeClass);
        } catch (\ReflectionException) {
            $attributes = [];
        }

        return ArrayList::collect($attributes)
            ->map(static fn (\ReflectionAttribute $a) => $a->newInstance())
            ->toList()
        ;
    }
}
