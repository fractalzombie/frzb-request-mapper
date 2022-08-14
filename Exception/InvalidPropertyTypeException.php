<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;

#[Immutable]
final class InvalidPropertyTypeException extends \InvalidArgumentException
{
    private const DEFAULT_NOT_SUPPORTED_TYPE_MESSAGE = '%s type is not supported';

    #[Pure]
    public static function notSupported(string $typeName, ?\Throwable $previous = null): self
    {
        $message = sprintf(self::DEFAULT_NOT_SUPPORTED_TYPE_MESSAGE, $typeName);

        return new self($message, previous: $previous);
    }
}
