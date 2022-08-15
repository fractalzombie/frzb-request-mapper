<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\TypeExtractor;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use FRZB\Component\RequestMapper\TypeExtractor\Extractor\TypeExtractorInterface as TypeExtractor;

#[AsAlias(TypeExtractorLocator::class)]
interface TypeExtractorLocatorInterface
{
    public function get(\ReflectionProperty|\ReflectionParameter $target): TypeExtractor;

    public function has(\ReflectionProperty|\ReflectionParameter $target): bool;
}
