<?php

declare(strict_types=1);


namespace FRZB\Component\RequestMapper\PropertyMapper;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use FRZB\Component\RequestMapper\PropertyMapper\Mapper\PropertyMapperInterface as PropertyMapper;

#[AsAlias(PropertyMapperLocator::class)]
interface PropertyMapperLocatorInterface
{
    public function get(\ReflectionProperty $property): PropertyMapper;
}
