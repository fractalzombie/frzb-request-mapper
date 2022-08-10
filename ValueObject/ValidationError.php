<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ValueObject;

use FRZB\Component\RequestMapper\Exception\ClassExtractorException;
use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\ConstraintViolationInterface as ConstraintViolation;

#[Immutable]
final class ValidationError implements ErrorInterface
{
    public function __construct(
        private readonly string $type,
        private readonly string $field,
        private readonly string $message,
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

    public static function fromConstraint(ConstraintViolation $violation): self
    {
        return new self(
            $violation->getConstraint()::class,
            $violation->getPropertyPath(),
            (string) $violation->getMessage()
        );
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
