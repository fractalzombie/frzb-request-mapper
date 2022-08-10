<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

/**
 * @method __invoke(\Throwable $e): ErrorContract
 */
interface FormatterInterface
{
    public static function getType(): string;
}
