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

namespace FRZB\Component\RequestMapper\ExceptionFormatter;

use Fp\Collections\HashMap;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\ExceptionFormatter\Formatter\FormatterInterface as Formatter;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AsService]
class ExceptionFormatterLocator implements ExceptionFormatterLocatorInterface
{
    /** @var HashMap<string, callable|Formatter> */
    private readonly HashMap $formatters;

    public function __construct(
        #[TaggedIterator(Formatter::class, defaultIndexMethod: 'getExceptionClass', defaultPriorityMethod: 'getPriority')]
        iterable $formatters,
    ) {
        $this->formatters = HashMap::collect($formatters);
    }

    public function get(\Throwable $e): callable|Formatter
    {
        return $this->formatters
            ->get($e::class)
            ->getOrElse($this->formatters->get(\Throwable::class)->get())
        ;
    }

    public function has(\Throwable $e): bool
    {
        return $this->formatters->get($e::class)->isSome();
    }
}
