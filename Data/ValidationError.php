<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

use FRZB\Component\RequestMapper\Exception\ErrorInvalidArgumentException;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Validator\ConstraintViolation as Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface as ConstraintViolation;

#[Immutable]
final class ValidationError implements ErrorInterface
{
    private string $type;
    private string $field;
    private string $message;

    public function __construct(string $type, string $field, string $message)
    {
        $this->type = $type;
        $this->field = $field;
        $this->message = $message;
    }

    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public static function fromConstraint(ConstraintViolation $violation): self
    {
        return match (true) {
            $violation instanceof Constraint => new self(
                $violation->getConstraint()::class,
                $violation->getPropertyPath(),
                (string) $violation->getMessage()
            ),
            default => throw ErrorInvalidArgumentException::fromConstraintValidation($violation),
        };
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
