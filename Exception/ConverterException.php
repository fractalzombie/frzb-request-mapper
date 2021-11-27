<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

final class ConverterException extends \LogicException
{
    public static function fromThrowable(\Throwable $e): self
    {
        return new self($e->getMessage(), (int) $e->getCode(), $e);
    }
}
