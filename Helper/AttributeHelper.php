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
use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class AttributeHelper
{
    private function __construct() {}

    public static function hasAttribute(\ReflectionClass|\ReflectionFunction|\ReflectionMethod|\ReflectionParameter|\ReflectionProperty $target, string $attributeClass): bool
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
    public static function getAttribute(\ReflectionClass|\ReflectionFunction|\ReflectionMethod|\ReflectionParameter|\ReflectionProperty $target, string $attributeClass): ?object
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
    public static function getAttributes(\ReflectionClass|\ReflectionFunction|\ReflectionMethod|\ReflectionParameter|\ReflectionProperty $target, string $attributeClass): array
    {
        return ArrayList::collect($target->getAttributes($attributeClass, \ReflectionAttribute::IS_INSTANCEOF))
            ->map(static fn (\ReflectionAttribute $a) => $a->newInstance())
            ->toList()
        ;
    }
}
