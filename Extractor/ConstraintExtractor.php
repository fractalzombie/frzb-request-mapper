<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Utils\ClassUtil;
use FRZB\Component\RequestMapper\Utils\ConstraintsUtil;
use FRZB\Component\RequestMapper\Utils\SerializerUtil;
use Symfony\Component\Validator\Constraints\Collection;

#[AsService]
class ConstraintExtractor
{
    /** @param class-string $class */
    public function extract(string $class): ?Collection
    {
        try {
            $rClass = new \ReflectionClass($class);
        } catch (\ReflectionException) {
            return null;
        }

        return ConstraintsUtil::createCollection($this->extractConstraints($rClass));
    }

    private function extractConstraints(\ReflectionClass $rClass): array
    {
        $constraints = [];

        if ($parentClass = $rClass->getParentClass()) {
            $constraints = $this->extractConstraints($parentClass);
        }

        foreach ($rClass->getProperties() as $property) {
            $propertyName = SerializerUtil::getSerializedNameAttribute($property)->getSerializedName();
            $constraints[$propertyName] = ClassUtil::isNotBuiltinAndExists($propertyClass = $property->getType()->getName())
                ? ConstraintsUtil::createCollection($this->extractConstraints(new \ReflectionClass($propertyClass)))
                : ConstraintsUtil::fromProperty($property);
        }

        return $constraints;
    }
}
