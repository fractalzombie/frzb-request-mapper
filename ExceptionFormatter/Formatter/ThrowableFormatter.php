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

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\Data\ErrorContract;
use FRZB\Component\RequestMapper\Data\FormattedError;
use Symfony\Component\HttpFoundation\Response;

#[AsService, AsTagged(FormatterInterface::class)]
class ThrowableFormatter implements FormatterInterface
{
    public function __invoke(\Throwable $e): ErrorContract
    {
        return new FormattedError('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR, trace: $e->getTrace());
    }

    public static function getExceptionClass(): string
    {
        return \Throwable::class;
    }

    public static function getPriority(): int
    {
        return 0;
    }
}
