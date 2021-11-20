<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Utils;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * @internal
 */
final class ConstraintsUtil
{
    public static function createCollection(array $fields, bool $allowExtraFields = true, bool $allowMissingFields = true): Collection
    {
        return new Collection(fields: $fields, allowExtraFields: $allowExtraFields, allowMissingFields: $allowMissingFields);
    }

    /** @return array<Constraint> */
    public static function fromProperty(\ReflectionProperty $rProperty): array
    {
        return array_map(
            static fn (\ReflectionAttribute $a) => $a->newInstance(),
            $rProperty->getAttributes(Constraint::class, \ReflectionAttribute::IS_INSTANCEOF)
        );
    }
}
