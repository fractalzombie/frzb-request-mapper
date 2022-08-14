<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\PropertyMapper\Mapper;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\PhpDocReader\Reader\ReaderInterface as PhpDocReader;
use FRZB\Component\RequestMapper\Helper\ConstraintsHelper;
use FRZB\Component\RequestMapper\Helper\PropertyHelper;

#[AsService, AsTagged(PropertyMapperInterface::class, priority: 1)]
final class BuiltinPropertyMapper implements PropertyMapperInterface
{
    public function __construct(
        private readonly PhpDocReader $reader,
    ) {
    }

    public function map(\ReflectionProperty $property, mixed $value): array
    {
        return [PropertyHelper::getName($property) => PropertyHelper::getTypeName($property)];
    }

    public function canMap(\ReflectionProperty $property): bool
    {
        return !ConstraintsHelper::hasArrayTypeAttribute($property) && !ConstraintsHelper::hasArrayDocBlock($property, $this->reader);
    }
}
