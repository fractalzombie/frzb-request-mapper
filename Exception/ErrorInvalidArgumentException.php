<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\ConstraintViolationInterface as ConstraintViolation;

/**
 * This error should never be thrown.
 */
class ErrorInvalidArgumentException extends \InvalidArgumentException
{
    #[Pure]
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
