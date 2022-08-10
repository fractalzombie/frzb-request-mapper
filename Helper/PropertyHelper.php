<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use Fp\Collections\ArrayList;
use FRZB\Component\PhpDocReader\Exception\ReaderException;
use FRZB\Component\PhpDocReader\Reader\ReaderInterface as PhpDocReader;
use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class PropertyHelper
{
    private function __construct()
    {
    }

    public static function getMapping(string $className, mixed $value, PhpDocReader $reader): array
    {
        try {
            $properties = ArrayList::collect((new \ReflectionClass($className))->getProperties());
        } catch (\ReflectionException) {
            return [];
        }

        return $properties
            ->map(static fn (\ReflectionProperty $p) => match (true) {
                ConstraintsHelper::hasArrayTypeAttribute($p) => [self::getName($p) => ArrayList::range(0, \count($value))->map(fn () => self::getTypeFromAttribute($p))->toArray()],
                ConstraintsHelper::hasArrayDocBlock($p, $reader) => [self::getName($p) => ArrayList::range(0, \count($value))->map(fn () => self::getTypeFromDocBlock($p, $reader))->toArray()],
                !ConstraintsHelper::hasArrayTypeAttribute($p) => [self::getName($p) => self::getTypeName($p)],
                !ConstraintsHelper::hasArrayDocBlock($p, $reader) => [self::getName($p) => self::getTypeName($p)],
                default => [self::getName($p) => []],
            })
            ->reduce(static fn (array $prev, array $next) => [...$prev, ...$next])
            ->getOrElse([])
        ;
    }

    public static function getTypeFromDocBlock(\ReflectionProperty|\ReflectionParameter $property, PhpDocReader $reader): ?string
    {
        try {
            return StringHelper::removeNotWordCharacters($reader->getPropertyClass($property));
        } catch (ReaderException) {
            return null;
        }
    }

    public static function getTypeFromAttribute(\ReflectionProperty|\ReflectionParameter $property): ?string
    {
        return StringHelper::removeNotWordCharacters(ConstraintsHelper::getArrayTypeAttribute($property)?->typeName ?? '');
    }

    public static function getName(\ReflectionProperty|\ReflectionParameter $property): string
    {
        return SerializerHelper::getSerializedNameAttribute($property)->getSerializedName();
    }

    public static function getTypeName(\ReflectionProperty|\ReflectionParameter $property): ?string
    {
        $type = $property->getType();

        $typeName = match (true) {
            $type instanceof \ReflectionIntersectionType => throw new \LogicException('ReflectionIntersectionType is not supported'),
            $type instanceof \ReflectionUnionType => throw new \LogicException('ReflectionUnionType is not supported'),
            $type instanceof \ReflectionNamedType => $type->getName(),
            default => null,
        };

        return StringHelper::removeNotWordCharacters($typeName ?? '');
    }
}
