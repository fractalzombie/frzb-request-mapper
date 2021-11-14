<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

use FRZB\Component\RequestMapper\Utils\StringUtil;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Validator\ConstraintViolation;

#[Immutable]
final class Error
{
    private string $field;
    private string $message;

    public function __construct(string $field, string $message)
    {
        $this->field = StringUtil::removeBrackets($field);
        $this->message = $message;
    }

    public static function fromConstraint(ConstraintViolation $violation): self
    {
        return new self($violation->getPropertyPath(), (string) $violation->getMessage());
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
