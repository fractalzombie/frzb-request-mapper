<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use Fp\Collections\ArrayList;
use Fp\Collections\Entry;
use Fp\Collections\HashMap;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\PhpDocReader\Exception\ReaderException;
use FRZB\Component\PhpDocReader\Reader\ReaderInterface as PhpDocReader;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use FRZB\Component\RequestMapper\Helper\ConstraintsHelper;
use FRZB\Component\RequestMapper\Helper\SerializerHelper;

#[AsService]
class ParametersExtractor
{
    public function __construct(
        private readonly PhpDocReader $reader,
    ) {
    }

    public function extract(string $class, array $parameters): array
    {
        return [...$parameters, ...$this->mapProperties($this->getPropertyMapping($class, $parameters), $parameters)];
    }

    private function mapProperties(array $properties, array $parameters): array
    {
        $params = HashMap::collect($parameters);
        $props = HashMap::collect($properties);

        $complexTypes = $props
            ->filter(static fn (Entry $propEntry) => \is_array($propEntry->value))
            ->map(fn (Entry $propEntry) => $this->mapPropertiesForArray($propEntry, $params))
            ->toAssocArray()
            ->getOrElse([])
        ;

        $classTypes = $props
            ->filter(static fn (Entry $propEntry) => !\is_array($propEntry->value) && ClassHelper::isNotBuiltinAndExists($propEntry->value))
            ->map(fn (Entry $propEntry) => $this->extract($propEntry->value, $params->get($propEntry->key)->getOrElse([])))
            ->toAssocArray()
            ->getOrElse([])
        ;

        $enumTypes = $props
            ->filter(static fn (Entry $propEntry) => !\is_array($propEntry->value) && ClassHelper::isEnum($propEntry->value))
            ->map(fn (Entry $propEntry) => $this->mapEnum($propEntry->value, $params->get($propEntry->key)->getOrElse(null)))
            ->toAssocArray()
            ->getOrElse([])
        ;

        $simpleTypes = $props
            ->filter(static fn (Entry $propEntry) => !\is_array($propEntry->value) && !ClassHelper::isNotBuiltinAndExists($propEntry->value))
            ->map(static fn (Entry $propEntry) => $params->get($propEntry->key)->getOrElse(null))
            ->toAssocArray()
            ->getOrElse([])
        ;

        return [...$complexTypes, ...$classTypes, ...$enumTypes, ...$simpleTypes];
    }

    private function mapEnum(string $enumClassName, mixed $value = null): ?\BackedEnum
    {
        return match (true) {
            is_subclass_of($enumClassName, \IntBackedEnum::class) && \is_int($value) => $enumClassName::tryFrom($value),
            is_subclass_of($enumClassName, \StringBackedEnum::class) && \is_string($value) => $enumClassName::tryFrom($value),
            default => null,
        };
    }

    private function mapPropertiesForArray(Entry $propEntry, HashMap $parameters): array
    {
        return HashMap::collect($parameters->get($propEntry->key)->get())
            ->map(static fn (Entry $paramEntry) => $propEntry->value[$paramEntry->key])
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
            ->map(fn (\ReflectionProperty $p) => match (true) {
                ConstraintsHelper::hasArrayTypeAttribute($p) => [SerializerHelper::getSerializedNameAttribute($p)->getSerializedName() => ArrayList::range(0, \count($value))->map(fn () => ConstraintsHelper::getArrayTypeAttribute($p)->typeName)->toArray()],
                ConstraintsHelper::hasArrayDocBlock($p, $this->reader) => [SerializerHelper::getSerializedNameAttribute($p)->getSerializedName() => ArrayList::range(0, \count($value))->map(fn () => $this->getPropertyTypeFromDocBlock($p))->toArray()],
                default => [SerializerHelper::getSerializedNameAttribute($p)->getSerializedName() => []],
            })
            ->reduce(static fn (array $prev, array $next) => [...$prev, ...$next])
            ->getOrElse([])
        ;

        $simpleTypes = $properties
            ->filter(static fn (\ReflectionProperty $p) => !ConstraintsHelper::hasArrayTypeAttribute($p))
            ->map(static fn (\ReflectionProperty $p) => [SerializerHelper::getSerializedNameAttribute($p)->getSerializedName() => $p->getType()?->/** @scrutinizer ignore-call */ getName()])
            ->reduce(static fn (array $prev, array $next) => [...$prev, ...$next])
            ->getOrElse([])
        ;

        return [...$complexTypes, ...$simpleTypes];
    }

    private function getPropertyTypeFromDocBlock(\ReflectionProperty $property): ?string
    {
        try {
            return $this->reader->getPropertyClass($property);
        } catch (ReaderException) {
            return null;
        }
    }
}
