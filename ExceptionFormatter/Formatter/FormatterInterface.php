<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

interface FormatterInterface
{
    public static function getExceptionClass(): string;

    public static function getPriority(): int;
}
