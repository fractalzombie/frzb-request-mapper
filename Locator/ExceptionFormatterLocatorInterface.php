<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Locator;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use FRZB\Component\RequestMapper\ExceptionFormatter\Formatter\FormatterInterface;

#[AsAlias(service: ExceptionFormatterLocator::class)]
interface ExceptionFormatterLocatorInterface
{
    public const EXCEPTION_FORMATTERS_TAG = 'frzb.request_mapper.exception_formatters';

    public function get(\Throwable $e): FormatterInterface;

    public function has(\Throwable $e): bool;
}
