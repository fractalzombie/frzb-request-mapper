<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints\Collection;

#[AsService]
class ParametersExtractor
{
    /**
     * @param class-string $class
     * @param array<string, mixed> $parameters
     * @param Collection|null $constraints
     *
     * @return array<string, mixed>
     */
    public function extract(string $class, array $parameters, ?Collection $constraints = null): array
    {
        $params = $parameters;
        $mapped = [];

        foreach ($this->getPropertyMapping($class) as $serializedName => $propertyName) {
            $mapped[$propertyName] = $params[$serializedName];
        }

        $constraintKeys = array_keys($constraints?->fields ?? []);
        $parameterKeys = array_keys($params);

        foreach (array_diff($constraintKeys, $parameterKeys) as $parameter) {
            $params[$parameter] = null;
        }

        if ($constraints && !empty($mapped)) {
            $constraints->allowExtraFields = true;
        }

        return array_merge($params, $mapped);
    }

    private function getPropertyMapping(string $class): array
    {
        $properties = [];

        /** @noinspection PhpUnhandledExceptionInspection */
        foreach ((new \ReflectionClass($class))->getProperties() as $property) {
            /** @var SerializedName[] $attributes */
            $attributes = array_map(
                static fn (\ReflectionAttribute $a) => $a->newInstance(),
                $property->getAttributes(SerializedName::class)
            );

            if ($attributes) {
                foreach ($attributes as $attribute) {
                    $properties[$attribute->getSerializedName()] = $property->getName();
                }
            }
        }

        return $properties;
    }
}
