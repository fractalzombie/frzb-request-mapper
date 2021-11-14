<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use FRZB\Component\RequestMapper\Data\ErrorContract;

#[AsAlias(service: ExceptionFormatter::class)]
interface ExceptionFormatterInterface
{
    public function format(\Throwable $e): ErrorContract;
}
