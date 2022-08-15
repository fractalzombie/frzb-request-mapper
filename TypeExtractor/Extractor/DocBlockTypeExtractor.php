<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\TypeExtractor\Extractor;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\PhpDocReader\Exception\ReaderException;
use FRZB\Component\PhpDocReader\Reader\ReaderInterface as PhpDocReader;
use FRZB\Component\RequestMapper\Helper\PropertyHelper;
use FRZB\Component\RequestMapper\Helper\StringHelper;

#[AsService, AsTagged(TypeExtractorInterface::class)]
class DocBlockTypeExtractor implements TypeExtractorInterface
{
    public function __construct(
        private readonly PhpDocReader $reader,
    ) {
    }

    public function extract(\ReflectionParameter|\ReflectionProperty $target): ?string
    {
        try {
            return StringHelper::removeNotWordCharacters($this->reader->getPropertyClass($target) ?? '');
        } catch (ReaderException) {
            return null;
        }
    }

    public function canExtract(\ReflectionParameter|\ReflectionProperty $target): bool
    {
        return 'array' === PropertyHelper::getTypeName($target) && null !== $this->reader->getPropertyClass($target);
    }
}
