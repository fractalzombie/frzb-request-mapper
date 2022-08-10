<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\PropertyMapper\Mapper;

interface PropertyMapperInterface
{
    public function map(\ReflectionProperty $property, mixed $value): array;

    public function canMap(\ReflectionProperty $property): bool;
}
