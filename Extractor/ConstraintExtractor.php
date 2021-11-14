<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;

#[AsService]
final class ConstraintExtractor
{
    /** @param class-string $class */
    public function extract(string $class): ?Collection
    {
        try {
            $rClass = new \ReflectionClass($class);
        } catch (\ReflectionException) {
            return null;
        }

        return new Collection($this->extractConstraints($rClass));
    }

    private function extractConstraints(\ReflectionClass $rClass): array
    {
        $constraints = [];

        if ($parentClass = $rClass->getParentClass()) {
            $constraints = $this->extractConstraints($parentClass);
        }

        foreach ($rClass->getProperties() as $property) {
            $attributes = $property->getAttributes();
            $attributes = array_map(static fn (\ReflectionAttribute $a) => $a->newInstance(), $attributes);
            $attributes = array_values(array_filter($attributes, static fn (object $a) => $a instanceof Constraint));
            $constraints[$property->getName()] = $attributes;
        }

        return $constraints;
    }
}
