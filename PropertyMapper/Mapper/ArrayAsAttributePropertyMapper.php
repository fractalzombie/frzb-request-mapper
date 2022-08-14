<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\PropertyMapper\Mapper;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\Helper\ConstraintsHelper;
use FRZB\Component\RequestMapper\Helper\PropertyHelper;

#[AsService, AsTagged(PropertyMapperInterface::class, priority: 3)]
final class ArrayAsAttributePropertyMapper implements PropertyMapperInterface
{
    public function map(\ReflectionProperty $property, mixed $value): array
    {
        return [
            PropertyHelper::getName($property) => array_map(static fn () => PropertyHelper::getTypeFromAttribute($property), range(0, \count($value))),
        ];
    }

    public function canMap(\ReflectionProperty $property): bool
    {
        return ConstraintsHelper::hasArrayTypeAttribute($property);
    }
}
