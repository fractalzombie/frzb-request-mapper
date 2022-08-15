<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\PropertyMapper\Mapper;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\Helper\PropertyHelper;

#[AsService, AsTagged(PropertyMapperInterface::class, priority: 0)]
class DefaultPropertyMapper implements PropertyMapperInterface
{
    public function map(\ReflectionProperty $property, mixed $value): array
    {
        return [PropertyHelper::getName($property) => []];
    }

    public function canMap(\ReflectionProperty $property): bool
    {
        return true;
    }
}
