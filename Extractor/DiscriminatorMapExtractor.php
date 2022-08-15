<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Exception\ClassExtractorException;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

#[AsService]
class DiscriminatorMapExtractor
{
    /** @throws ClassExtractorException */
    public function extract(string $className, array $payload): string
    {
        if ($discriminatorMap = ClassHelper::getAttribute($className, DiscriminatorMap::class)) {
            $property = $discriminatorMap->getTypeProperty();
            $mapping = $discriminatorMap->getMapping();
            $parameter = $payload[$property] ?? throw ClassExtractorException::fromDiscriminatorMapWhenParameterIsNull($discriminatorMap);
            $className = $mapping[$parameter] ?? throw ClassExtractorException::fromDiscriminatorMapWhenParameterInvalid($discriminatorMap);
        }

        return $className;
    }
}
