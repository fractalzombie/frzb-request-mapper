<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use FRZB\Component\RequestMapper\Data\ErrorContract;

interface FormatterInterface
{
    public function format(\Throwable $e): ErrorContract;

    public static function getExceptionClass(): string;

    public static function getPriority(): int;
}
