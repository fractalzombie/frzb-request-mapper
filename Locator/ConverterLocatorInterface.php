<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Locator;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use FRZB\Component\RequestMapper\Converter\TypeConverter;
use FRZB\Component\RequestMapper\Exception\ConverterContainerException;
use FRZB\Component\RequestMapper\Exception\ConverterNotFoundException;

#[AsAlias(service: ConverterLocator::class)]
interface ConverterLocatorInterface
{
    public const REQUEST_MAPPER_CONVERTERS_TAG = 'frzb.request_mapper.converter';

    /**
     * @throws ConverterNotFoundException
     * @throws ConverterContainerException
     */
    public function get(string $type): TypeConverter;

    public function has(string $type): bool;
}
