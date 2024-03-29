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
use FRZB\Component\RequestMapper\Attribute\RequestBody;
use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;

/** @internal */
#[Immutable]
final class RequestBodyHelper
{
    private const REQUEST_POSTFIXES = ['Request', 'Dto', 'DTO'];

    private function __construct() {}

    public static function getAttribute(\ReflectionParameter $parameter, array $attributes): ?RequestBody
    {
        return $attributes[PropertyHelper::getName($parameter)]
            ?? $attributes[PropertyHelper::getTypeName($parameter)]
            ?? self::searchAttribute($parameter, $attributes);
    }

    #[Pure]
    public static function mapAttribute(RequestBody $paramConverter): array
    {
        return [$paramConverter->argumentName ?? $paramConverter->requestClass => $paramConverter];
    }

    /** @return array<string, RequestBody> */
    public static function fromReflectionAttributes(\ReflectionAttribute ...$attributes): array
    {
        return ArrayList::collect($attributes)
            ->map(static fn (\ReflectionAttribute $ra): RequestBody => $ra->newInstance())
            ->map(self::mapAttribute(...))
            ->toMergedArray()
        ;
    }

    public static function fromReflectionParameter(\ReflectionParameter $parameter): ?RequestBody
    {
        $parameterName = $parameter->getName();
        $parameterType = PropertyHelper::getTypeName($parameter);

        return match (true) {
            !$parameterType, !ClassHelper::isNameContains($parameterType, ...self::REQUEST_POSTFIXES) => null,
            default => new RequestBody(requestClass: $parameterType, argumentName: $parameterName),
        };
    }

    private static function searchAttribute(\ReflectionParameter $parameter, array $attributes): ?RequestBody
    {
        return ArrayList::collect($attributes)
            ->first(static fn (RequestBody $pc): bool => $pc->equals($parameter))
            ->getOrElse(self::fromReflectionParameter($parameter))
        ;
    }
}
