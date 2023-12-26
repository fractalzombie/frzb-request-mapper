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

namespace FRZB\Component\RequestMapper\PropertyMapper\Mapper;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\Helper\PropertyHelper;
use FRZB\Component\RequestMapper\TypeExtractor\TypeExtractorLocatorInterface;

#[AsService, AsTagged(PropertyMapperInterface::class, priority: 1)]
class BuiltinPropertyMapper implements PropertyMapperInterface
{
    public function __construct(
        private readonly TypeExtractorLocatorInterface $extractorLocator,
    ) {}

    public function map(\ReflectionProperty $property, mixed $value): array
    {
        return [PropertyHelper::getName($property) => PropertyHelper::getTypeName($property)];
    }

    public function canMap(\ReflectionProperty $property): bool
    {
        return !$this->extractorLocator->has($property);
    }
}
