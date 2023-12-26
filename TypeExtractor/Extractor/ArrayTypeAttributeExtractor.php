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
