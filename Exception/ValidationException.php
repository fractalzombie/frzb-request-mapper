<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use FRZB\Component\RequestMapper\Data\Error;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\ConstraintViolationListInterface as ConstraintViolationList;

class ValidationException extends \Exception
{
    public const DEFAULT_MESSAGE = 'Validation error';

    /** @var Error[] */
    private array $errors = [];

    #[Pure]
    public function __construct(string $message = self::DEFAULT_MESSAGE, int|string $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromConstrainViolationList(ConstraintViolationList $violations): self
    {
        return (new self())->mergeWithConstraintViolationList($violations);
    }

    /**
     * @param Error ...$errors
     *
     * @return ValidationException
     */
    public function addErrors(Error ...$errors): self
    {
        $this->errors = array_merge($this->errors, $errors);

        return $this;
    }

    public static function fromConstrainViolationListWithPrevious(ConstraintViolationList $violations, \Throwable $previous): self
    {
        return (new self(self::DEFAULT_MESSAGE, $previous->getCode(), $previous))
            ->mergeWithConstraintViolationList($violations)
        ;
    }

    public static function fromErrors(Error ...$errors): self
    {
        return (new self())->addErrors(...$errors);
    }

    /** @return Error[] */
    public function getErrors(): array
    {
        return $this->errors;
    }

    private function mergeWithConstraintViolationList(ConstraintViolationList $violations): self
    {
        foreach ($violations as $violation) {
            $this->addErrors(Error::fromConstraint($violation));
        }

        return $this;
    }
}
