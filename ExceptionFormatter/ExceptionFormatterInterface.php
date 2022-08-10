<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use FRZB\Component\RequestMapper\ValueObject\ErrorContract as ContractError;

#[AsAlias(service: ExceptionFormatter::class)]
interface ExceptionFormatterInterface
{
    public function format(\Throwable $e): ContractError;
}
