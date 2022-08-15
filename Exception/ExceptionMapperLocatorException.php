<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;

#[Immutable]
final class ExceptionMapperLocatorException extends \DomainException
{
    private const NOT_FOUND_MESSAGE = 'No mapper found for exception "%s"';

    #[Pure(true)]
    public static function notFound(\Throwable $previous, bool $wrapCallable = true): callable|self
    {
        $message = sprintf(self::NOT_FOUND_MESSAGE, $previous::class);
        $exception = new self($message, previous: $previous);

        return $wrapCallable ? static fn () => throw $exception : $exception;
    }
}
