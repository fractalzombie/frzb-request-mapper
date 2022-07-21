<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use FRZB\Component\RequestMapper\ExceptionFormatter\Formatter\FormatterInterface;

#[AsAlias(service: ExceptionFormatterLocator::class)]
interface ExceptionFormatterLocatorInterface
{
    public function get(\Throwable $e): FormatterInterface|callable;

    public function has(\Throwable $e): bool;
}
