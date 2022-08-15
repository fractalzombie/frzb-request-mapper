<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

class TypeExtractorLocatorException extends \LogicException
{
    private const NOT_FOUND_MESSAGE = 'Type extractor not found for "%s:%s"';

    public static function notFound(\ReflectionProperty|\ReflectionParameter $target, bool $wrapCallable = true, ?\Throwable $previous = null): callable|self
    {
        $message = sprintf(self::NOT_FOUND_MESSAGE, $target->getDeclaringClass()->getName(), $target->getName());
        $exception = new self($message, previous: $previous);

        return $wrapCallable ? static fn () => throw $exception : $exception;
    }
}
