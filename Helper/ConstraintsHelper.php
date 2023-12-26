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

use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;

/** @internal */
#[Immutable]
final class ConstraintsHelper
{
    private function __construct() {}

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
