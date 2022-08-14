<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;

#[Immutable]
final class HelperException extends \LogicException
{
    private const NO_CLASS_PROPERTY_MESSAGE = 'No property "%s" in class "%s"';
    private const NO_METHOD_PARAMETER_MESSAGE = 'Method "%s::%s" has no "%s" parameter';

    #[Pure]
    public static function noClassProperty(string $className, string $propertyName, bool $wrapCallable = true, ?\Throwable $previous = null): callable|self
    {
        $message = sprintf(self::NO_CLASS_PROPERTY_MESSAGE, $propertyName, $className);
        $exception = new self($message, previous: $previous);

        return $wrapCallable ? static fn () => throw $exception : $exception;
    }

    #[Pure]
    public static function noMethodParameter(string $className, string $classMethod, string $parameterName, bool $wrapCallable = true, ?\Throwable $previous = null): callable|self
    {
        $message = sprintf(self::NO_METHOD_PARAMETER_MESSAGE, $className, $classMethod, $parameterName);
        $exception = new self($message, previous: $previous);

        return $wrapCallable ? static fn () => throw $exception : $exception;
    }
}
