<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class ArrayType
{
    public function __construct(
        public readonly string $typeName,
    ) {
    }
}
