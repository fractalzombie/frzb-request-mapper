<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Attribute;

use JetBrains\PhpStorm\Deprecated;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
#[Deprecated('Refactoring, name policy', RequestBody::class)]
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
final class ParamConverter extends RequestBody
{
}
