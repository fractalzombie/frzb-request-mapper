<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use Fp\Collections\ArrayList;
use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class AttributeHelper
{
    private function __construct()
    {
    }

    public static function hasAttribute(\ReflectionProperty|\ReflectionClass|\ReflectionMethod|\ReflectionFunction|\ReflectionParameter $target, string $attributeClass): bool
    {
        return null !== self::getAttribute($target, $attributeClass);
    }

    /**
     * @template T
     *
     * @param class-string<T> $attributeClass
     *
     * @return null|T
     */
    public static function getAttribute(\ReflectionProperty|\ReflectionClass|\ReflectionMethod|\ReflectionFunction|\ReflectionParameter $target, string $attributeClass): ?object
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
     * @return T[]
     */
    public static function getAttributes(\ReflectionProperty|\ReflectionClass|\ReflectionMethod|\ReflectionFunction|\ReflectionParameter $target, string $attributeClass): array
    {
        return ArrayList::collect($target->getAttributes($attributeClass, \ReflectionAttribute::IS_INSTANCEOF))
            ->map(static fn (\ReflectionAttribute $a) => $a->newInstance())
            ->toArray()
        ;
    }
}
