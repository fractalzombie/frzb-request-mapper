<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\TypeExtractor\Extractor;

interface TypeExtractorInterface
{
    public function extract(\ReflectionProperty|\ReflectionParameter $target): ?string;

    public function canExtract(\ReflectionProperty|\ReflectionParameter $target): bool;
}
