<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use Fp\Collections\ArrayList;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use FRZB\Component\RequestMapper\Helper\ConstraintsHelper;
use FRZB\Component\RequestMapper\Helper\PropertyHelper;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;

#[AsService]
class ConstraintExtractor
{
    public function extract(string $className, array $parameters = []): ?Collection
    {
        try {
            return ConstraintsHelper::createCollection($this->extractConstraints($className, $parameters));
        } catch (\ReflectionException) {
            return null;
        }
    }

    /** @throws \ReflectionException */
    public function extractConstraints(string $className, array $parameters = []): array
    {
        $constraints = [];
        $reflectionClass = new \ReflectionClass($className);

        if ($parentClass = $reflectionClass->getParentClass()) {
            $constraints = $this->extractConstraints($parentClass->getName());
        }

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = PropertyHelper::getName($property);
            $propertyValue = $parameters[$propertyName] ?? [];
            $propertyTypeName = PropertyHelper::getTypeName($property);
            $arrayTypeName = ConstraintsHelper::getArrayTypeAttribute($property)?->typeName;

            $constraints[$propertyName] = match (true) {
                ConstraintsHelper::hasArrayTypeAttribute($property) => ArrayList::collect($propertyValue)->map(fn () => new All($this->extract($arrayTypeName, $propertyValue)))->toArray(),
                ClassHelper::isNotBuiltinAndExists($propertyTypeName) => $this->extract($propertyTypeName, $propertyValue),
                default => ConstraintsHelper::fromProperty($property),
            };
        }

        return $constraints;
    }
}
