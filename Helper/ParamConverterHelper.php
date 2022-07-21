<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use Fp\Collections\ArrayList;
use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;

/** @internal */
#[Immutable]
final class ParamConverterHelper
{
    private const REQUEST_POSTFIXES = ['Request', 'Dto', 'DTO'];

    private function __construct()
    {
    }

    public static function getAttribute(\ReflectionParameter $parameter, array $attributes): ?ParamConverter
    {
        return $attributes[$parameter->getName()]
            ?? $attributes[$parameter->getType()?->/** @scrutinizer ignore-call */ getName()]
            ?? self::searchAttribute($parameter, $attributes);
    }

    #[Pure]
    public static function mapParamConverter(ParamConverter $paramConverter): array
    {
        return [$paramConverter->getParameterName() ?? $paramConverter->getParameterClass() => $paramConverter];
    }

    public static function fromReflectionAttribute(\ReflectionAttribute $attribute): ParamConverter
    {
        return $attribute->newInstance();
    }

    /** @return array<string, ParamConverter> */
    public static function fromReflectionAttributes(\ReflectionAttribute ...$attributes): array
    {
        return ArrayList::collect($attributes)
            ->map(static fn (\ReflectionAttribute $ra): ParamConverter => self::fromReflectionAttribute($ra))
            ->map(static fn (ParamConverter $pc): array => self::mapParamConverter($pc))
            ->reduce(static fn (array $prev, array $next) => [...$prev, ...$next])
            ->getOrElse([])
        ;
    }

    public static function fromReflectionParameter(\ReflectionParameter $parameter): ?ParamConverter
    {
        $parameterName = $parameter->getName();
        $parameterType = $parameter->getType()?->/** @scrutinizer ignore-call */ getName();

        return match (true) {
            !$parameterType, !ClassHelper::isNameContains($parameterType, ...self::REQUEST_POSTFIXES) => null,
            default => new ParamConverter(parameterClass: $parameterType, parameterName: $parameterName),
        };
    }

    private static function searchAttribute(\ReflectionParameter $parameter, array $attributes): ?ParamConverter
    {
        return ArrayList::collect($attributes)
            ->first(static fn (ParamConverter $pc): bool => $pc->equals($parameter))
            ->getOrElse(self::fromReflectionParameter($parameter))
        ;
    }
}
