<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use FRZB\Component\RequestMapper\Data\ErrorContract;
use FRZB\Component\RequestMapper\Data\FormattedError;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AutoconfigureTag(FormatterInterface::class)]
class HttpExceptionFormatter implements FormatterInterface
{
    #[Pure]
    public function __invoke(HttpException $e): ErrorContract
    {
        return new FormattedError($e->getMessage(), $e->getStatusCode(), trace: $e->getTrace());
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
