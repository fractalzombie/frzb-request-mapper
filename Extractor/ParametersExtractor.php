<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use Fp\Collections\ArrayList;
use Fp\Collections\Entry;
use Fp\Collections\HashMap;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use FRZB\Component\RequestMapper\Helper\ConstraintsHelper;
use FRZB\Component\RequestMapper\Helper\SerializerHelper;

#[AsService]
class ParametersExtractor
{
    public function extract(string $class, array $parameters): array
    {
        return [...$parameters, ...$this->mapProperties($this->getPropertyMapping($class, $parameters), $parameters)];
    }

    private function mapProperties(array $properties, array $parameters): array
    {
        $params = HashMap::collect($parameters);
        $props = HashMap::collect($properties);

        $complexTypes = $props
            ->filter(fn (Entry $propEntry) => \is_array($propEntry->value))
            ->map(fn (Entry $propEntry) => $this->mapPropertiesForArray($propEntry, $params))
            ->toAssocArray()
            ->getOrElse([])
        ;

        $classTypes = $props
            ->filter(fn (Entry $propEntry) => !\is_array($propEntry->value) && ClassHelper::isNotBuiltinAndExists($propEntry->value))
            ->map(fn (Entry $propEntry) => $this->extract($propEntry->value, $params->get($propEntry->key)->getOrElse([])))
            ->toAssocArray()
            ->getOrElse([])
        ;

        $simpleTypes = $props
            ->filter(fn (Entry $propEntry) => !\is_array($propEntry->value) && !ClassHelper::isNotBuiltinAndExists($propEntry->value))
            ->map(fn (Entry $propEntry) => $params->get($propEntry->key)->getOrElse(null))
            ->toAssocArray()
            ->getOrElse([])
        ;

        return [...$complexTypes, ...$classTypes, ...$simpleTypes];
    }

    private function mapPropertiesForArray(Entry $propEntry, HashMap $parameters): array
    {
        return HashMap::collect($parameters->get($propEntry->key)->get())
            ->map(fn (Entry $paramEntry) => $propEntry->value[$paramEntry->key])
            ->map(fn (Entry $paramEntry) => $this->extract($paramEntry->value, $parameters->get($propEntry->key)->get()[$paramEntry->key] ?? []))
            ->toAssocArray()
            ->get()
            ;
    }

    private function getPropertyMapping(string $className, mixed $value): array
    {
        try {
            $properties = ArrayList::collect((new \ReflectionClass($className))->getProperties());
        } catch (\ReflectionException) {
            $properties = ArrayList::empty();
        }

        $complexTypes = $properties
            ->filter(fn (\ReflectionProperty $p) => ConstraintsHelper::hasArrayTypeAttribute($p))
            ->map(fn (\ReflectionProperty $p) => [SerializerHelper::getSerializedNameAttribute($p)->getSerializedName() => ArrayList::range(1, \count($value))->map(fn () => ConstraintsHelper::getArrayTypeAttribute($p)->typeName)->toArray()])
            ->reduce(fn (array $prev, array $next) => [...$prev, ...$next])
            ->getOrElse([])
        ;

        $simpleTypes = $properties
            ->filter(fn (\ReflectionProperty $p) => !ConstraintsHelper::hasArrayTypeAttribute($p))
            ->map(fn (\ReflectionProperty $p) => [SerializerHelper::getSerializedNameAttribute($p)->getSerializedName() => $p->getType()?->/** @scrutinizer ignore-call */ getName()])
            ->reduce(fn (array $prev, array $next) => [...$prev, ...$next])
            ->getOrElse([])
        ;

        return [...$complexTypes, ...$simpleTypes];
    }
}
