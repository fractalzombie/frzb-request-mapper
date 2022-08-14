<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionMapper\Mapper;

use FRZB\Component\RequestMapper\Data\ErrorInterface as Error;

/**
 * @method Error __invoke(\Throwable $exception, array $payload)
 */
interface ExceptionMapperInterface
{
    public static function getType(): string;
}
