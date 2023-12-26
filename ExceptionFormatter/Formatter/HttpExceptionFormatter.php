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
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsService, AsTagged(FormatterInterface::class)]
class HttpExceptionFormatter implements FormatterInterface
{
    #[Pure]
    public function __invoke(HttpException $e): ErrorContract
    {
        return new FormattedError($e->getMessage(), $e->getStatusCode(), trace: $e->getTrace());
    }

    public static function getExceptionClass(): string
    {
        return HttpException::class;
    }

    public static function getPriority(): int
    {
        return 2;
    }
}
