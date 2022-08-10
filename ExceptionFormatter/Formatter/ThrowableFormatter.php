<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use FRZB\Component\DependencyInjection\Attribute\AsIgnored;
use FRZB\Component\RequestMapper\ValueObject\ErrorContract;
use FRZB\Component\RequestMapper\ValueObject\FormattedError;
use Symfony\Component\HttpFoundation\Response;

#[AsIgnored]
class ThrowableFormatter implements FormatterInterface
{
    public function __invoke(\Throwable $e): ErrorContract
    {
        return new FormattedError('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR, trace: $e->getTrace());
    }

    public static function getType(): string
    {
        return \Throwable::class;
    }
}
