<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\Data\ErrorContract;
use FRZB\Component\RequestMapper\Data\FormattedError;
use Symfony\Component\HttpFoundation\Response;

#[AsService, AsTagged(FormatterInterface::class)]
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
