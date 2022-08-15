<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\PropertyMapper\Mapper;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\Helper\PropertyHelper;
use FRZB\Component\RequestMapper\TypeExtractor\TypeExtractorLocatorInterface;

#[AsService, AsTagged(PropertyMapperInterface::class, priority: 1)]
class BuiltinPropertyMapper implements PropertyMapperInterface
{
    public function __construct(
        private readonly TypeExtractorLocatorInterface $extractorLocator,
    ) {
    }

    public function map(\ReflectionProperty $property, mixed $value): array
    {
        return [PropertyHelper::getName($property) => PropertyHelper::getTypeName($property)];
    }

    public function canMap(\ReflectionProperty $property): bool
    {
        return !$this->extractorLocator->has($property);
    }
}
