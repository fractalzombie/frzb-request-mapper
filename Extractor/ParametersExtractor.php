<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use Fp\Collections\Entry;
use Fp\Collections\HashMap;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\PhpDocReader\Reader\ReaderInterface as PhpDocReader;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use FRZB\Component\RequestMapper\Helper\PropertyHelper;

#[AsService]
class ParametersExtractor
{
    public function __construct(
        private readonly PhpDocReader $reader,
    ) {
    }

    public function extract(string $class, array $parameters): array
    {
        return [...$parameters, ...$this->mapProperties(PropertyHelper::getMapping($class, $parameters, $this->reader), $parameters)];
    }

    private function mapProperties(array $properties, array $parameters): array
    {
        return HashMap::collect($properties)
            ->map(fn (Entry $e) => match (true) {
                \is_array($e->value) => $this->mapPropertiesForArray($e->key, $e->value, $parameters),
                ClassHelper::isNotBuiltinAndExists($e->value) => $this->extract($e->value, $parameters[$e->key] ?? []),
                ClassHelper::isEnum($e->value) => $this->mapEnum($e->value, $parameters[$e->key] ?? null),
                !ClassHelper::isNotBuiltinAndExists($e->value) => $parameters[$e->key] ?? null,
                default => null,
            })
            ->toAssocArray()
            ->getOrElse([])
        ;
    }

    private function mapEnum(string $enumClassName, mixed $value = null): ?\BackedEnum
    {
        return match (true) {
            is_subclass_of($enumClassName, \IntBackedEnum::class) && \is_int($value) => $enumClassName::tryFrom($value),
            is_subclass_of($enumClassName, \StringBackedEnum::class) && \is_string($value) => $enumClassName::tryFrom($value),
            default => null,
        };
    }

    private function mapPropertiesForArray(string $key, array $value, array $parameters): array
    {
        return HashMap::collect($parameters[$key] ?? [])
            ->map(static fn (Entry $e) => $e->value[$key])
            ->map(fn (Entry $e) => $this->extract($e->value, $parameters[$key][$e->key] ?? []))
            ->toAssocArray()
            ->get()
        ;
    }
}
