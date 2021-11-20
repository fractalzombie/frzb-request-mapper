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
    /** @param array<string, ParamConverter> $attributes */
    public static function getAttribute(\ReflectionParameter $parameter, array $attributes): ?ParamConverter
    {
        return $attributes[$parameter->getName()]
            ?? $attributes[$parameter->getType()?->getName()]
            ?? self::searchAttribute($parameter, $attributes);
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public static function fromReflectionAttribute(\ReflectionAttribute $attribute): ParamConverter
    {
        return $attribute->newInstance();
    }

    #[Pure]
    public static function mapParamConverter(ParamConverter $paramConverter): array
    {
        return [$paramConverter->getName() ?? $paramConverter->getClass() => $paramConverter];
    }

    /**
     * @param \ReflectionAttribute ...$attributes
     *
     * @return array<string, ParamConverter>
     */
    public static function fromReflectionAttributes(\ReflectionAttribute ...$attributes): array
    {
        $mapReflection = static fn (\ReflectionAttribute $ra): ParamConverter => self::fromReflectionAttribute($ra);
        $mapAttributes = static fn (ParamConverter $pc): array => self::mapParamConverter($pc);

        return array_merge(...array_map($mapAttributes, array_map($mapReflection, $attributes)));
    }

    private static function searchAttribute(\ReflectionParameter $parameter, array $attributes): ?ParamConverter
    {
        $filteredAttributes = array_filter(
            $attributes,
            static fn (ParamConverter $pc) => $parameter->getType()?->getName() === $pc->getClass() || $parameter->getName() === $pc->getName()
        );

        return current($filteredAttributes) ?: null;
    }
}
