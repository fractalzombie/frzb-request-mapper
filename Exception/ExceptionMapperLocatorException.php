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

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;

#[Immutable]
final class ExceptionMapperLocatorException extends \DomainException
{
    private const NOT_FOUND_MESSAGE = 'No mapper found for exception "%s"';

    #[Pure(true)]
    public static function notFound(\Throwable $previous, bool $wrapCallable = true): callable|self
    {
        $message = sprintf(self::NOT_FOUND_MESSAGE, $previous::class);
        $exception = new self($message, previous: $previous);

        return $wrapCallable ? static fn () => throw $exception : $exception;
    }
}
