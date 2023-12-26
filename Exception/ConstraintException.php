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
use Symfony\Component\Validator\ConstraintViolationListInterface as ConstraintViolationList;

#[Immutable]
final class ConstraintException extends \LogicException
{
    private const DEFAULT_MESSAGE = 'Constraint Exception';

    #[Pure]
    private function __construct(private readonly ConstraintViolationList $violations, ?\Throwable $previous = null)
    {
        parent::__construct(self::DEFAULT_MESSAGE, 0, $previous);
    }

    #[Pure]
    public static function fromConstraintViolationList(ConstraintViolationList $violationList, ?\Throwable $previous = null): self
    {
        return new self($violationList, previous: $previous);
    }

    public function getViolations(): ConstraintViolationList
    {
        return $this->violations;
    }
}
