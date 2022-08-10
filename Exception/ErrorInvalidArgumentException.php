<?php

declare(strict_types=1);

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
