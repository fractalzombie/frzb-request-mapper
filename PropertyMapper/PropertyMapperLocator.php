<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\PropertyMapper;

use Fp\Collections\ArrayList;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Exception\PropertyMapperLocatorException;
use FRZB\Component\RequestMapper\PropertyMapper\Mapper\PropertyMapperInterface as PropertyMapper;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AsService]
final class PropertyMapperLocator implements PropertyMapperLocatorInterface
{
    /** @var ArrayList<PropertyMapper> */
    private readonly ArrayList $mappers;

    public function __construct(
        #[TaggedIterator(PropertyMapper::class)] iterable $mappers,
    ) {
        $this->mappers = ArrayList::collect($mappers);
    }

    public function get(\ReflectionProperty $property): PropertyMapper
    {
        return $this->mappers
            ->first(fn (PropertyMapper $pm) => $pm->canMap($property))
            ->getOrThrow(PropertyMapperLocatorException::throwMapperNotFound($property))
        ;
    }
}
