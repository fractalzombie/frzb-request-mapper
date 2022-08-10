<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\ConstraintViolationListInterface as ConstraintViolationList;

#[Immutable]
final class ConstraintException extends \LogicException
{
    private const DEFAULT_MESSAGE = 'ConstraintException';

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
