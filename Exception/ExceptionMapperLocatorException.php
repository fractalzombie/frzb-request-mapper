<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;

#[Immutable]
final class ExceptionMapperLocatorException extends \DomainException
{
    private const NO_MAPPER_FOUND_MESSAGE = 'No mapper found for exception "%s"';

    #[Pure(true)]
    public static function noMapperFound(\Throwable $previous, bool $wrapCallable = true): callable|self
    {
        $message = sprintf(self::NO_MAPPER_FOUND_MESSAGE, $previous::class);
        $exception = new self($message, previous: $previous);

        return $wrapCallable ? static fn () => throw $exception : $exception;
    }
}
