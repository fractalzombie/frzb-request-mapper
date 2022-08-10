<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ClassMapper;

use Fp\Collections\ArrayList;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\PropertyMapper\PropertyMapperLocatorInterface as PropertyMapperLocator;

#[AsService]
final class ClassMapper implements ClassMapperInterface
{
    public function __construct(
        private readonly PropertyMapperLocator $mapperLocator,
    ) {
    }

    public function map(string $className, mixed $value): array
    {
        try {
            return ArrayList::collect((new \ReflectionClass($className))->getProperties())
                ->map(fn (\ReflectionProperty $property) => $this->mapperLocator->get($property)->map($property, $value) ?? [])
                ->reduce(array_merge(...))
                ->getOrElse([])
            ;
        } catch (\ReflectionException) {
            return [];
        }
    }
}
