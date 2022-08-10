<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\ValueObject\ErrorContract;
use FRZB\Component\RequestMapper\ValueObject\FormattedError;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsService, AsTagged(FormatterInterface::class, priority: 1)]
class HttpExceptionFormatter implements FormatterInterface
{
    #[Pure]
    public function __invoke(HttpException $e): ErrorContract
    {
        return new FormattedError($e->getMessage(), $e->getStatusCode(), trace: $e->getTrace());
    }

    public static function getType(): string
    {
        return HttpException::class;
    }
}
