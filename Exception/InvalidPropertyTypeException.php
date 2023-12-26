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
final class InvalidPropertyTypeException extends \InvalidArgumentException
{
    private const DEFAULT_NOT_SUPPORTED_TYPE_MESSAGE = '%s type is not supported';

    #[Pure]
    public static function notSupported(string $typeName, ?\Throwable $previous = null): self
    {
        $message = sprintf(self::DEFAULT_NOT_SUPPORTED_TYPE_MESSAGE, $typeName);

        return new self($message, previous: $previous);
    }
}
