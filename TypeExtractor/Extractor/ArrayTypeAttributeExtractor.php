<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\TypeExtractor\Extractor;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\Attribute\ArrayType;
use FRZB\Component\RequestMapper\Helper\AttributeHelper;
use FRZB\Component\RequestMapper\Helper\PropertyHelper;

#[AsService, AsTagged(TypeExtractorInterface::class)]
class ArrayTypeAttributeExtractor implements TypeExtractorInterface
{
    public function extract(\ReflectionParameter|\ReflectionProperty $target): ?string
    {
        return AttributeHelper::getAttribute($target, ArrayType::class)->typeName;
    }

    public function canExtract(\ReflectionParameter|\ReflectionProperty $target): bool
    {
        return 'array' === PropertyHelper::getTypeName($target) && null !== AttributeHelper::getAttribute($target, ArrayType::class);
    }
}
