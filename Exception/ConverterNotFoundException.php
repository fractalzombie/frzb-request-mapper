<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Pure;

class ConverterNotFoundException extends \OutOfBoundsException
{
    #[Pure]
    public function __construct(string $converterType, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Converter with type "%s" is not found.', $converterType), $code, $previous);
    }
}
