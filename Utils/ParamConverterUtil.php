<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Utils;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use JetBrains\PhpStorm\Pure;

/**
 * @internal
 */
final class ParamConverterUtil
{
    private const REQUEST_POSTFIXES = ['Request', 'Dto', 'DTO'];

    /** @param array<class-string, ParamConverter|string> $attributes */
    public static function getAttribute(\ReflectionParameter $parameter, array $attributes): ?ParamConverter
    {
        return $attributes[$parameter->getName()]
            ?? $attributes[$parameter->getType()?->getName()]
            ?? self::searchAttribute($parameter, $attributes);
    }

    #[Pure]
    public static function mapParamConverter(ParamConverter $paramConverter): array
    {
        return [$paramConverter->getParameterName() ?? $paramConverter->getParameterClass() => $paramConverter];
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public static function fromReflectionAttribute(\ReflectionAttribute $attribute): ParamConverter
    {
        return $attribute->newInstance();
    }

    /**
     * @param \ReflectionAttribute ...$attributes
     *
     * @return array<class-string|string|string, ParamConverter>
     */
    public static function fromReflectionAttributes(\ReflectionAttribute ...$attributes): array
    {
        $mapReflection = static fn (\ReflectionAttribute $ra): ParamConverter => self::fromReflectionAttribute($ra);
        $mapAttributes = static fn (ParamConverter $pc): array => self::mapParamConverter($pc);

        return array_merge(...array_map($mapAttributes, array_map($mapReflection, $attributes)));
    }

    public static function fromReflectionParameter(\ReflectionParameter $parameter): ?ParamConverter
    {
        return match (true) {
            !$parameter->getType()?->getName(), !ClassUtil::isNameContains($parameter->getType()?->getName(), ...self::REQUEST_POSTFIXES) => null,
            default => new ParamConverter(parameterClass: $parameter->getType()?->getName(), parameterName: $parameter->getName()),
        };
    }

    private static function searchAttribute(\ReflectionParameter $parameter, array $attributes): ?ParamConverter
    {
        $filteredAttributes = array_filter($attributes, static fn (ParamConverter $pc): bool => $pc->equals($parameter));

        return current($filteredAttributes) ?: self::fromReflectionParameter($parameter);
    }
}
