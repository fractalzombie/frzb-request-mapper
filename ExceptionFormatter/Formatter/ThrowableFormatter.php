<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use FRZB\Component\RequestMapper\Data\ErrorContract;
use FRZB\Component\RequestMapper\Locator\ExceptionFormatterLocatorInterface as ExceptionFormatterLocator;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag(ExceptionFormatterLocator::EXCEPTION_FORMATTERS_TAG)]
final class ThrowableFormatter implements FormatterInterface
{
    #[Pure]
    public function format(\Throwable $e): ErrorContract
    {
        return new ErrorContract(
            'Internal Server Error',
            Response::HTTP_INTERNAL_SERVER_ERROR,
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
