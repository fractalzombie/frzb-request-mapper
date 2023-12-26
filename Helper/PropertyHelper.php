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

use FRZB\Component\RequestMapper\Exception\InvalidPropertyTypeException;
use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class PropertyHelper
{
    private function __construct() {}

    public static function getName(\ReflectionParameter|\ReflectionProperty $property): string
    {
        return SerializerHelper::getSerializedNameAttribute($property)->getSerializedName();
    }

    public static function getTypeName(\ReflectionParameter|\ReflectionProperty $property): ?string
    {
        $type = $property->getType();

        $typeName = match (true) {
            $type instanceof \ReflectionIntersectionType => throw InvalidPropertyTypeException::notSupported(\ReflectionIntersectionType::class),
            $type instanceof \ReflectionUnionType => throw InvalidPropertyTypeException::notSupported(\ReflectionUnionType::class),
            $type instanceof \ReflectionNamedType => $type->getName(),
            default => null,
        };

        return StringHelper::removeNotWordCharacters($typeName ?? '');
    }
}
