<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

use Symfony\Component\Validator\ConstraintViolationInterface as ConstraintViolation;

interface ErrorInterface extends \Stringable
{
    public static function fromConstraint(ConstraintViolation $violation): self;

    public function getType(): string;

    public function getField(): string;

    public function getMessage(): ?string;
}
