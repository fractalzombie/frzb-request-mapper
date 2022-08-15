<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\PropertyMapper\Mapper;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\Helper\PropertyHelper;
use FRZB\Component\RequestMapper\TypeExtractor\Extractor\ArrayTypeAttributeExtractor;

#[AsService, AsTagged(PropertyMapperInterface::class, priority: 3)]
class ArrayAsAttributePropertyMapper implements PropertyMapperInterface
{
    public function __construct(
        private readonly ArrayTypeAttributeExtractor $extractor,
    ) {
    }

    public function map(\ReflectionProperty $property, mixed $value): array
    {
        return [PropertyHelper::getName($property) => array_map(fn () => $this->extractor->extract($property), range(0, \count($value)))];
    }

    public function canMap(\ReflectionProperty $property): bool
    {
        return $this->extractor->canExtract($property);
    }
}
