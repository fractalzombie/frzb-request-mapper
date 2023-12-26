<?php

declare(strict_types=1);

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *
 * Copyright (c) 2023 Mykhailo Shtanko fractalzombie@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE.MD
 * file that was distributed with this source code.
 */

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
        #[TaggedIterator(TypeExtractor::class)]
        iterable $extractors,
    ) {
        $this->extractors = ArrayList::collect($extractors);
    }

    public function get(\ReflectionParameter|\ReflectionProperty $target): TypeExtractor
    {
        return $this->extractors
            ->first(static fn (TypeExtractor $extractor) => $extractor->canExtract($target))
            ->getOrElse(fn () => throw TypeExtractorLocatorException::notFound($target))
        ;
    }

    public function has(\ReflectionParameter|\ReflectionProperty $target): bool
    {
        return $this->extractors
            ->first(static fn (TypeExtractor $extractor) => $extractor->canExtract($target))
            ->isSome()
        ;
    }
}
