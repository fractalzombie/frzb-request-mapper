<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use FRZB\Component\RequestMapper\Data\ErrorContract;
use FRZB\Component\RequestMapper\Locator\ExceptionFormatterLocatorInterface as ExceptionFormatterLocator;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AutoconfigureTag(ExceptionFormatterLocator::EXCEPTION_FORMATTERS_TAG)]
final class HttpExceptionFormatter implements FormatterInterface
{
    public function format(\Throwable $e): ErrorContract
    {
        $status = ($e instanceof HttpException) ? $e->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = $e->getMessage();

        return new ErrorContract($message, $status);
    }

    public static function getExceptionClass(): string
    {
        return HttpException::class;
    }

    public static function getPriority(): int
    {
        return 2;
    }
}
