<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Exception\ClassExtractorException;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

#[AsService]
class DiscriminatorMapExtractor
{
    /**
     * @param class-string $class
     *
     * @throws ClassExtractorException
     *
     * @return class-string
     */
    public function extract(string $class, array $parameters): string
    {
        if ($discriminatorMap = $this->getDiscriminatorMapAttribute($class)) {
            $property = $discriminatorMap->getTypeProperty();
            $mapping = $discriminatorMap->getMapping();
            $parameter = $parameters[$property] ?? throw ClassExtractorException::createWhenParameterIsNull($discriminatorMap);
            $class = $mapping[$parameter] ?? throw ClassExtractorException::createWhenParameterInvalid($discriminatorMap);
        }

        return $class;
    }

    /** @param class-string $class */
    private function getDiscriminatorMapAttribute(string $class): ?DiscriminatorMap
    {
        try {
            $discriminators = array_map(
                static fn (\ReflectionAttribute $attribute) => $attribute->newInstance(),
                (new \ReflectionClass($class))->getAttributes(DiscriminatorMap::class)
            );

            return \count($discriminators) ? current($discriminators) : null;
        } catch (\ReflectionException) {
            return null;
        }
    }
}
