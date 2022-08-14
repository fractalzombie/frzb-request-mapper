<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;

#[Immutable]
final class ConverterException extends \LogicException
{
    public static function fromThrowable(\Throwable $e): self
    {
        return new self($e->getMessage(), (int) $e->getCode(), $e);
    }

    #[Pure]
    public static function nullableParameterClass(): self
    {
        return new self('Property "argumentName" can not be null');
    }
}
