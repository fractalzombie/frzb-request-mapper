<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

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
}
