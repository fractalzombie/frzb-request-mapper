<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Pure;

final class ConverterException extends \LogicException
{
    public static function fromThrowable(\Throwable $e): self
    {
        return new self($e->getMessage(), (int) $e->getCode(), $e);
    }

    #[Pure]
    public static function nullableParameterClass(): self
    {
        return new self('Property "parameterClass" can not be null');
    }
}
