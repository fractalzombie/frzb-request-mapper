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
final class TypeErrorInvalidArgumentException extends \InvalidArgumentException
{
    private const MESSAGE_TEMPLATE = 'Params have not needed values "%s"';

    #[Pure]
    private function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromParams(array $params, ?\Throwable $previous = null): self
    {
        $message = sprintf(self::MESSAGE_TEMPLATE, implode(', ', array_keys($params)));

        return new self($message, $previous?->getCode() ?? 0, $previous);
    }
}
