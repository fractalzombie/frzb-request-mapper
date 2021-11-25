<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use FRZB\Component\RequestMapper\Data\ContractError;
use FRZB\Component\RequestMapper\Data\ContractErrorInterface;
use FRZB\Component\RequestMapper\Locator\ExceptionFormatterLocatorInterface as ExceptionFormatterLocator;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag(ExceptionFormatterLocator::EXCEPTION_FORMATTERS_TAG)]
class ThrowableFormatter implements FormatterInterface
{
    public function format(\Throwable $e): ContractErrorInterface
    {
        return new ContractError(
            'Internal Server Error',
            Response::HTTP_INTERNAL_SERVER_ERROR,
            trace: $e->getTrace()
        );
    }

    public static function getExceptionClass(): string
    {
        return \Throwable::class;
    }

    public static function getPriority(): int
    {
        return 0;
    }
}
