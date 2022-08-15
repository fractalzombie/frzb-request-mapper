<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\TypeExtractor;

use Fp\Collections\ArrayList;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Exception\TypeExtractorLocatorException;
use FRZB\Component\RequestMapper\TypeExtractor\Extractor\TypeExtractorInterface as TypeExtractor;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AsService]
class TypeExtractorLocator implements TypeExtractorLocatorInterface
{
    /** @var ArrayList<TypeExtractor> */
    private readonly ArrayList $extractors;

    public function __construct(
        #[TaggedIterator(TypeExtractor::class)] iterable $extractors,
    ) {
        $this->extractors = ArrayList::collect($extractors);
    }

    public function get(\ReflectionProperty|\ReflectionParameter $target): TypeExtractor
    {
        return $this->extractors
            ->first(static fn (TypeExtractor $extractor) => $extractor->canExtract($target))
            ->getOrThrow(TypeExtractorLocatorException::notFound($target))
        ;
    }

    public function has(\ReflectionParameter|\ReflectionProperty $target): bool
    {
        return $this->extractors
            ->first(static fn (TypeExtractor $extractor) => $extractor->canExtract($target))
            ->isNonEmpty()
        ;
    }
}
