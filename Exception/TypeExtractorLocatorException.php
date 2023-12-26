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

use JetBrains\PhpStorm\Pure;

class TypeExtractorLocatorException extends \LogicException
{
    private const NOT_FOUND_MESSAGE = 'Type extractor not found for "%s:%s"';

    #[Pure]
    public static function notFound(\ReflectionParameter|\ReflectionProperty $target, bool $wrapCallable = true, ?\Throwable $previous = null): callable|self
    {
        $message = sprintf(self::NOT_FOUND_MESSAGE, $target->getDeclaringClass()->getName(), $target->getName());
        $exception = new self($message, previous: $previous);

        return $wrapCallable ? static fn () => throw $exception : $exception;
    }
}
