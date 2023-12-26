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

#[Immutable]
final class PropertyMapperLocatorException extends \LogicException
{
    private const NOT_FOUND_MESSAGE = 'Mapper not found for %s::%s';

    public static function notFound(\ReflectionProperty $property, bool $wrapCallable = true, ?\Throwable $previous = null): callable|self
    {
        $className = $property->getDeclaringClass()->getName();
        $propertyName = $property->getName();
        $message = sprintf(self::NOT_FOUND_MESSAGE, $className, $propertyName);
        $exception = new self($message, previous: $previous);

        return $wrapCallable ? static fn () => throw $exception : throw $exception;
    }
}
