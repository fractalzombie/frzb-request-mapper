<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use FRZB\Component\RequestMapper\Helper\ConstraintsHelper;
use FRZB\Component\RequestMapper\Helper\SerializerHelper;
use Symfony\Component\Validator\Constraints\Collection;

#[AsService]
class ConstraintExtractor
{
    public function extract(string $class): ?Collection
    {
        try {
            return ConstraintsHelper::createCollection($this->extractConstraints(new \ReflectionClass($class)));
        } catch (\ReflectionException) {
            return null;
        }
    }

    /** @throws \ReflectionException */
    private function extractConstraints(\ReflectionClass $rClass): array
    {
        $constraints = [];

        if ($parentClass = $rClass->getParentClass()) {
            $constraints = $this->extractConstraints($parentClass);
        }

        foreach ($rClass->getProperties() as $property) {
            $propertyName = SerializerHelper::getSerializedNameAttribute($property)->getSerializedName();
            $propertyClass = $property->getType()?->/** @scrutinizer ignore-call */ getName();
            $constraints[$propertyName] = ClassHelper::isNotBuiltinAndExists($propertyClass)
                ? ConstraintsHelper::createCollection($this->extractConstraints(new \ReflectionClass($propertyClass)))
                : ConstraintsHelper::fromProperty($property);
        }

        return $constraints;
    }
}
