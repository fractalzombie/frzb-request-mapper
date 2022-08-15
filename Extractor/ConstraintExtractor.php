<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use Fp\Collections\ArrayList;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use FRZB\Component\RequestMapper\Helper\ConstraintsHelper;
use FRZB\Component\RequestMapper\Helper\PropertyHelper;
use FRZB\Component\RequestMapper\TypeExtractor\TypeExtractorLocatorInterface as TypeExtractorLocator;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;

#[AsService]
class ConstraintExtractor
{
    public function __construct(
        private readonly TypeExtractorLocator $extractorLocator,
    ) {
    }

    public function extract(string $className, array $payload = []): ?Collection
    {
        try {
            return ConstraintsHelper::createCollection($this->extractConstraints($className, $payload));
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

            $constraints[$propertyName] = match (true) {
                $this->extractorLocator->has($property) => ArrayList::collect($propertyValue)->map(fn () => new All($this->extract($this->extractorLocator->get($property)->extract($property), $propertyValue)))->toArray(),
                ClassHelper::isNotBuiltinAndExists($propertyTypeName) => $this->extract($propertyTypeName, $propertyValue),
                default => ConstraintsHelper::fromProperty($property),
            };
        }

        return $constraints;
    }
}
