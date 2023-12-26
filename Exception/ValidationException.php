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

use FRZB\Component\RequestMapper\Data\ErrorInterface as Error;
use FRZB\Component\RequestMapper\Data\ValidationError;
use JetBrains\PhpStorm\Deprecated;
use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\ConstraintViolationInterface as ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface as ConstraintViolationList;

#[Immutable]
final class ValidationException extends \LogicException
{
    public const DEFAULT_MESSAGE = 'Validation Exception';

    /** @var Error[] */
    public readonly array $errors;

    #[Pure]
    private function __construct(Error ...$errors)
    {
        parent::__construct(self::DEFAULT_MESSAGE);
        $this->errors = $errors;
    }

    public static function fromConstraintViolationList(ConstraintViolationList $violationList): self
    {
        return self::fromConstraintViolations(...$violationList);
    }

    public static function fromConstraintViolations(ConstraintViolation ...$violations): self
    {
        return self::fromErrors(...array_map(static fn (ConstraintViolation $violation) => ValidationError::fromConstraint($violation), $violations));
    }

    #[Pure]
    public static function fromErrors(Error ...$errors): self
    {
        return new self(...$errors);
    }

    /** @return Error[] */
    #[Deprecated('PHP has readonly properties now', 'public readonly property $errors', '8.1')]
    public function getErrors(): array
    {
        return $this->errors;
    }
}
