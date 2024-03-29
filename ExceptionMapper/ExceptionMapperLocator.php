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

namespace FRZB\Component\RequestMapper\ExceptionMapper;

use Fp\Collections\HashMap;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Exception\ExceptionMapperLocatorException;
use FRZB\Component\RequestMapper\ExceptionMapper\Mapper\ExceptionMapperInterface;
use FRZB\Component\RequestMapper\ExceptionMapper\Mapper\ExceptionMapperInterface as ExceptionMapper;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AsService]
final class ExceptionMapperLocator implements ExceptionMapperLocatorInterface
{
    private readonly HashMap $mappers;

    public function __construct(#[TaggedIterator(ExceptionMapperInterface::class, defaultIndexMethod: 'getType')] iterable $mappers)
    {
        $this->mappers = HashMap::collect($mappers);
    }

    public function get(\Throwable $exception): ExceptionMapper
    {
        return $this->mappers
            ->get($exception::class)
            ->getOrElse(fn () => throw ExceptionMapperLocatorException::notFound($exception))
        ;
    }
}
