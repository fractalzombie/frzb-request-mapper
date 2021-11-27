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
            $rClass = new \ReflectionClass($class);
        } catch (\ReflectionException) {
            return null;
        }

        return ConstraintsHelper::createCollection($this->extractConstraints($rClass));
    }

    private function extractConstraints(\ReflectionClass $rClass): array
    {
        $constraints = [];

        if ($parentClass = $rClass->getParentClass()) {
            $constraints = $this->extractConstraints($parentClass);
        }

        foreach ($rClass->getProperties() as $property) {
            $propertyName = SerializerHelper::getSerializedNameAttribute($property)->getSerializedName();
            $constraints[$propertyName] = ClassHelper::isNotBuiltinAndExists($propertyClass = $property->getType()?->getName())
                ? ConstraintsHelper::createCollection($this->extractConstraints(new \ReflectionClass($propertyClass)))
                : ConstraintsHelper::fromProperty($property);
        }

        return $constraints;
    }
}
