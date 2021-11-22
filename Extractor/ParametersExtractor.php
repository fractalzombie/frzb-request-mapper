<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Utils\ClassUtil;
use FRZB\Component\RequestMapper\Utils\SerializerUtil;
use Symfony\Component\Validator\Constraints\Collection;

#[AsService]
class ParametersExtractor
{
    /**
     * @param class-string         $class
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>
     */
    public function extract(string $class, array $parameters, ?Collection $constraints = null): array
    {
        $params = $parameters;
        $mapped = $this->mapProperties($this->getPropertyMapping($class), $parameters, $constraints);

        $constraintKeys = array_keys($constraints?->fields ?? []);
        $parameterKeys = array_keys($params);

        foreach (array_diff($constraintKeys, $parameterKeys) as $parameter) {
            unset($params[$parameter]);
        }

        return array_merge($params, $mapped);
    }

    private function mapProperties(array $properties, array $parameters, ?Collection $constraints = null): array
    {
        $mapped = [];

        foreach ($properties as $serializedName => [$propertyName, $propertyType, $isAllowsNull]) {
            $value = $parameters[$serializedName] ?? null;
            $isComplexType = ClassUtil::isNotBuiltinAndExists($propertyType);
            $mapped[$serializedName] = $isComplexType ? $this->extract($propertyType, $value ?? []) : $value;
        }

        return $mapped;
    }

    private function getPropertyMapping(string $class): array
    {
        $mapping = [];

        try {
            $properties = (new \ReflectionClass($class))->getProperties();
        } catch (\ReflectionException) {
            $properties = [];
        }

        foreach ($properties as $property) {
            $serializedName = SerializerUtil::getSerializedNameAttribute($property)->getSerializedName();
            $mapping[$serializedName] = [$property->getName(), $property->getType()?->getName(), $property->getType()?->allowsNull()];
        }

        return $mapping;
    }
}
