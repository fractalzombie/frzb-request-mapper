<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Helper\ClassHelper;

#[AsService]
class ParametersExtractor
{
    public function extract(string $class, array $parameters): array
    {
        return array_merge($parameters, $this->mapProperties(ClassHelper::getPropertyMapping($class), $parameters));
    }

    private function mapProperties(array $properties, array $parameters): array
    {
        $map = fn (string $pt, string $pn): array => match (true) {
            ClassHelper::isNotBuiltinAndExists($pt) => [$pn => $this->extract($pt, $parameters[$pn] ?? [])],
            default => [$pn => $parameters[$pn] ?? null],
        };

        return array_merge(...array_map($map, array_values($properties), array_keys($properties)));
    }
}
