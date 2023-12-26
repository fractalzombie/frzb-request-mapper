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

namespace FRZB\Component\RequestMapper\PropertyMapper;

use Fp\Collections\ArrayList;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Exception\PropertyMapperLocatorException;
use FRZB\Component\RequestMapper\PropertyMapper\Mapper\PropertyMapperInterface as PropertyMapper;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AsService]
class PropertyMapperLocator implements PropertyMapperLocatorInterface
{
    /** @var ArrayList<PropertyMapper> */
    private readonly ArrayList $mappers;

    public function __construct(
        #[TaggedIterator(PropertyMapper::class)]
        iterable $mappers,
    ) {
        $this->mappers = ArrayList::collect($mappers);
    }

    public function get(\ReflectionProperty $property): PropertyMapper
    {
        return $this->mappers
            ->first(fn (PropertyMapper $pm) => $pm->canMap($property))
            ->getOrElse(fn () => throw PropertyMapperLocatorException::notFound($property))
        ;
    }
}
