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
final class ExceptionMapperException extends \LogicException
{
    private const NOT_MATCHED_GROUP_MESSAGE = 'Regex group "%s" not matched in "%s" exception with message "%s"';

    public static function notMatchedGroup(string $groupName, \Throwable $previous): self
    {
        $message = sprintf(self::NOT_MATCHED_GROUP_MESSAGE, $groupName, $previous::class, $previous->getMessage());

        return new self($message, previous: $previous);
    }
}
