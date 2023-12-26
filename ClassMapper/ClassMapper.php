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

namespace FRZB\Component\RequestMapper\ClassMapper;

use Fp\Collections\ArrayList;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\PropertyMapper\PropertyMapperLocatorInterface as PropertyMapperLocator;

#[AsService]
final class ClassMapper implements ClassMapperInterface
{
    public function __construct(
        private readonly PropertyMapperLocator $mapperLocator,
    ) {}

    public function map(string $className, mixed $value): array
    {
        try {
            return ArrayList::collect((new \ReflectionClass($className))->getProperties())
                ->map(fn (\ReflectionProperty $property) => $this->mapperLocator->get($property)->map($property, $value) ?? [])
                ->toMergedArray()
            ;
        } catch (\ReflectionException) {
            return [];
        }
    }
}
