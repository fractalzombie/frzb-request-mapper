<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

use FRZB\Component\RequestMapper\Utils\StringUtil;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Validator\ConstraintViolation;

#[Immutable]
final class Error
{
    private string $type;
    private string $field;
    private string $message;

    public function __construct(string $type, string $field, string $message)
    {
        $this->type = $type;
        $this->field = StringUtil::removeBrackets($field);
        $this->message = $message;
    }

    public static function fromConstraint(ConstraintViolation $violation): self
    {
        return new self($violation->getConstraint()::class, $violation->getPropertyPath(), $violation->getMessage());
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
