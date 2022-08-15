<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class PropertyMapperLocatorException extends \LogicException
{
    private const NOT_FOUND_MESSAGE = 'Mapper not found for %s::%s';

    public static function notFound(\ReflectionProperty $property, bool $wrapCallable = true, ?\Throwable $previous = null): callable|self
    {
        $className = $property->getDeclaringClass()->getName();
        $propertyName = $property->getName();
        $message = sprintf(self::NOT_FOUND_MESSAGE, $className, $propertyName);
        $exception = new self($message, previous: $previous);

        return $wrapCallable ? static fn () => throw $exception : throw $exception;
    }
}
