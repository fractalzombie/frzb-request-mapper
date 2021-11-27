<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

use FRZB\Component\RequestMapper\Exception\ClassExtractorException;
use FRZB\Component\RequestMapper\Exception\ErrorInvalidArgumentException;
use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\ConstraintViolation as Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface as ConstraintViolation;

#[Immutable]
final class ValidationError implements ErrorInterface
{
    public function __construct(
        private string $type,
        private string $field,
        private string $message
    ) {
    }

    public function __toString()
    {
        return sprintf('type: "%s", field: "%s", message: "%s"', $this->type, $this->field, $this->message);
    }

    #[Pure]
    public static function fromTypeAndClassExtractorException(string $type, ClassExtractorException $e): self
    {
        return new self($type, $e->getProperty(), $e->getMessage());
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
