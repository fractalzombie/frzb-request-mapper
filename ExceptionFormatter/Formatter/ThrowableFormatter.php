<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use FRZB\Component\RequestMapper\Data\ErrorContract;
use FRZB\Component\RequestMapper\Data\FormattedError;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag(FormatterInterface::class)]
class ThrowableFormatter implements FormatterInterface
{
    public function __invoke(\Throwable $e): ErrorContract
    {
        return new FormattedError('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR, trace: $e->getTrace());
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
