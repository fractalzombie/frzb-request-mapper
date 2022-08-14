<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionMapper;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use FRZB\Component\RequestMapper\ExceptionMapper\Mapper\ExceptionMapperInterface as ExceptionMapper;

#[AsAlias(ExceptionMapperLocator::class)]
interface ExceptionMapperLocatorInterface
{
    public function get(\Throwable $exception): ExceptionMapper;
}
