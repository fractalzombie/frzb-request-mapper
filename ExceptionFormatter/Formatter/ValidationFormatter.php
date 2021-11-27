<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use FRZB\Component\RequestMapper\Data\ErrorContract;
use FRZB\Component\RequestMapper\Data\ErrorInterface as Error;
use FRZB\Component\RequestMapper\Data\FormattedError;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use FRZB\Component\RequestMapper\Locator\ExceptionFormatterLocatorInterface as ExceptionFormatterLocator;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag(ExceptionFormatterLocator::EXCEPTION_FORMATTERS_TAG)]
class ValidationFormatter implements FormatterInterface
{
    public function __invoke(ValidationException $e): ErrorContract
    {
        return new FormattedError($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, self::formatErrors(...$e->getErrors()), $e->getTrace());
    }

    public static function getExceptionClass(): string
    {
        return ValidationException::class;
    }

    public static function getPriority(): int
    {
        return 1;
    }

    private static function formatErrors(Error ...$errors): array
    {
        return array_merge(...array_map(static fn (Error $error) => [$error->getField() => $error->getMessage()], $errors));
    }
}
