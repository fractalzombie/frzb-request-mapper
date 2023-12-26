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

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use FRZB\Component\RequestMapper\TypeExtractor\Extractor\TypeExtractorInterface as TypeExtractor;

#[AsAlias(TypeExtractorLocator::class)]
interface TypeExtractorLocatorInterface
{
    public function get(\ReflectionParameter|\ReflectionProperty $target): TypeExtractor;

    public function has(\ReflectionParameter|\ReflectionProperty $target): bool;
}
