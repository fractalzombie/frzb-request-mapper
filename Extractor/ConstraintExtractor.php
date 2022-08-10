<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use Fp\Collections\ArrayList;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use FRZB\Component\RequestMapper\Helper\ConstraintsHelper;
use FRZB\Component\RequestMapper\Helper\PropertyHelper;
use FRZB\Component\RequestMapper\Helper\SerializerHelper;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Exception\NoSuchMetadataException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ClassMetadataImpl;
use Symfony\Component\Validator\Mapping\ClassMetadataInterface as ClassMetadata;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface as MetadataFactory;
use Symfony\Component\Validator\Mapping\PropertyMetadata as PropertyMetadataImpl;
use Symfony\Component\Validator\Mapping\PropertyMetadataInterface as PropertyMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface as Validator;

#[AsService(arguments: ['$metadataFactory' => Validator::class])]
class ConstraintExtractor
{
    public function __construct(
        private readonly MetadataFactory $metadataFactory,
    ) {
    }

    public function extract(string $class, array $parameters = []): ?Collection
    {
        try {
            return ConstraintsHelper::createCollection($this->extractConstraints($class, $parameters));
        } catch (NoSuchMetadataException|\ReflectionException) {
            return null;
        }
    }

    /** @throws NoSuchMetadataException|\ReflectionException */
    public function extractConstraints(string $class, array $parameters = []): array
    {
        $constraints = [];
//        $classMetadata = $this->getClassMetadataFor($class);
        $reflectionClass = new \ReflectionClass($class);

        if ($parentClass = $reflectionClass->getParentClass()) {
            $constraints = $this->extractConstraints($parentClass->getName());
        }

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();
//            $propertyMetadata = $this->getPropertyMetadataFor($classMetadata, $propertyName);
//            $propertyReflectionMember = $this->getPropertyReflectionMember($class, $propertyMetadata);
            $propertySerializedName = SerializerHelper::getSerializedNameAttribute($property)?->getSerializedName();
            $propertyTypeName = PropertyHelper::getTypeName($property);
            $propertyValue = $parameters[$propertySerializedName] ?? $parameters[$propertyName] ?? [];
//            dump($propertyValue, $propertyTypeName, $parameters);
            $arrayTypeName = ConstraintsHelper::getArrayTypeAttribute($property)?->typeName;

//            $constraints[$propertySerializedName] = match (true) {
//                ConstraintsHelper::hasArrayTypeAttribute($propertyReflectionMember) => ArrayList::collect($propertyValue)->map(fn () => new All($this->extract($arrayTypeName, $propertyValue)))->toArray(),
//                ClassHelper::isNotBuiltinAndExists($propertyTypeName) => $this->extractConstraints($propertyTypeName),
//                default => $propertyMetadata->getConstraints(),
//            };

            $constraints[$propertySerializedName] = match (true) {
                ConstraintsHelper::hasArrayTypeAttribute($property) => ArrayList::collect($propertyValue)->map(fn () => new All($this->extract($arrayTypeName, $propertyValue)))->toArray(),
                ClassHelper::isNotBuiltinAndExists($propertyTypeName) => $this->extract($propertyTypeName),
                default => ConstraintsHelper::fromProperty($property),
            };
        }

        return $constraints;
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    private function getClassMetadataFor(string $class): ClassMetadata
    {
        return $this->metadataFactory->getMetadataFor($class) ?? new ClassMetadataImpl($class);
    }

    private function getPropertyMetadataFor(ClassMetadata $classMetadata, string $propertyName): PropertyMetadata
    {
        return ArrayList::collect($classMetadata->getPropertyMetadata($propertyName))
            ->firstElement()
            ->getOrElse(new PropertyMetadataImpl($classMetadata->getClassName(), $propertyName))
        ;
    }

    private function getPropertyReflectionMember(string $class, PropertyMetadata $propertyMetadata): \ReflectionMethod|\ReflectionProperty
    {
        return $propertyMetadata->getReflectionMember($class);
    }
}
