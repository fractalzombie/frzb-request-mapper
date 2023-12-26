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
use PHPUnit\Framework\Attributes\CodeCoverageIgnore;
use Symfony\Component\Validator\ConstraintViolationInterface as ConstraintViolation;

#[Immutable]
#[CodeCoverageIgnore('This error should never be thrown')]
final class ErrorInvalidArgumentException extends \InvalidArgumentException
{
    public static function fromConstraintValidation(ConstraintViolation $violation, ?\Throwable $previous = null): self
    {
        $message = sprintf(
            'Validation Constraint: [%s] [%s] [%s]',
            $violation->getPropertyPath(),
            (string) $violation->getCode(),
            (string) $violation->getMessage(),
        );

        return new self($message, previous: $previous);
    }
}
