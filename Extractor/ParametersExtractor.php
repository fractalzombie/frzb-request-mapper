<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\ClassMapper\ClassMapperInterface as ClassMapper;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use JetBrains\PhpStorm\Pure;

#[AsService]
class ParametersExtractor
{
    public function __construct(
        private readonly ClassMapper $classMapper,
    ) {
    }

    public function extract(string $className, array $payload): array
    {
        return [...$payload, ...$this->mapProperties($this->classMapper->map($className, $payload), $payload)];
    }

    private function mapProperties(array $properties, array $parameters): array
    {
        $mapping = [];

        foreach ($properties as $propertyName => $propertyType) {
            $propertyValue = $parameters[$propertyName] ?? null;

            $mapping[$propertyName] = match (true) {
                \is_array($propertyType) => array_map(fn (array $parameters) => $this->extract(current($propertyType), $parameters), $propertyValue ?? []),
                ClassHelper::isNotBuiltinAndExists($propertyType) => $this->extract($propertyType, $propertyValue ?? []),
                ClassHelper::isEnum($propertyType) => $this->mapEnum($propertyType, $propertyValue) ?? $propertyValue,
                default => $propertyValue,
            };
        }

        return $mapping;
    }

    #[Pure]
    private function mapEnum(string $enumClassName, mixed $value = null): ?\BackedEnum
    {
        return match (true) {
            is_subclass_of($enumClassName, \IntBackedEnum::class) && \is_int($value) => $enumClassName::tryFrom($value),
            is_subclass_of($enumClassName, \StringBackedEnum::class) && \is_string($value) => $enumClassName::tryFrom($value),
            default => null,
        };
    }
}
