<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use FRZB\Component\PhpDocReader\Reader\ReaderInterface as PhpDocReader;
use FRZB\Component\RequestMapper\Attribute\ArrayType;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;

/** @internal */
#[Immutable]
final class ConstraintsHelper
{
    private function __construct()
    {
    }

    public static function createCollection(array $fields, bool $allowExtraFields = true, bool $allowMissingFields = true): Collection
    {
        return new Collection(fields: $fields, allowExtraFields: $allowExtraFields, allowMissingFields: $allowMissingFields);
    }

    /** @return array<Constraint> */
    public static function fromProperty(\ReflectionProperty $rProperty): array
    {
        return AttributeHelper::getAttributes($rProperty, Constraint::class);
    }

    public static function getArrayTypeAttribute(\ReflectionProperty $rProperty): ?ArrayType
    {
        return AttributeHelper::getAttribute($rProperty, ArrayType::class);
    }

    public static function hasArrayTypeAttribute(\ReflectionProperty $rProperty): bool
    {
        return 'array' === PropertyHelper::getTypeName($rProperty) && null !== AttributeHelper::getAttribute($rProperty, ArrayType::class);
    }

    public static function hasArrayDocBlock(\ReflectionProperty $rProperty, PhpDocReader $reader): bool
    {
        return 'array' === PropertyHelper::getTypeName($rProperty) && null !== $reader->getPropertyClass($rProperty);
    }
}
