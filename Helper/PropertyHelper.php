<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use FRZB\Component\RequestMapper\Exception\InvalidPropertyTypeException;
use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class PropertyHelper
{
    private function __construct()
    {
    }

    public static function getName(\ReflectionProperty|\ReflectionParameter $property): string
    {
        return SerializerHelper::getSerializedNameAttribute($property)->getSerializedName();
    }

    public static function getTypeName(\ReflectionProperty|\ReflectionParameter $property): ?string
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
