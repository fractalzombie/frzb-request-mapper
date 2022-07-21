<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use FRZB\Component\PhpDocReader\Reader\ReaderInterface as PhpDocReader;
use FRZB\Component\RequestMapper\Attribute\ArrayType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * @internal
 */
final class ConstraintsHelper
{
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
        return 'array' === $rProperty->getType()?->getName() && null !== AttributeHelper::getAttribute($rProperty, ArrayType::class);
    }

    public static function hasArrayDocBlock(\ReflectionProperty $rProperty, PhpDocReader $reader): bool
    {
        return 'array' === $rProperty->getType()?->getName() && null !== $reader->getPropertyClass($rProperty);
    }
}
