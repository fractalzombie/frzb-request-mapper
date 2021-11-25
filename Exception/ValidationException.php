<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use FRZB\Component\RequestMapper\Data\ErrorInterface as Error;
use FRZB\Component\RequestMapper\Data\ValidationError;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\ConstraintViolationInterface as ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface as ConstraintViolationList;

final class ValidationException extends \LogicException
{
    public const DEFAULT_MESSAGE = 'Validation error';

    /** @var Error[] */
    private array $errors;

    #[Pure]
    private function __construct(Error ...$errors)
    {
        parent::__construct(self::DEFAULT_MESSAGE);
        $this->errors = $errors;
    }

    /** @noinspection PhpParamsInspection */
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
    public function getErrors(): array
    {
        return $this->errors;
    }
}
