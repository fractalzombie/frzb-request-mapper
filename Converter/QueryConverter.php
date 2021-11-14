<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Converter;

use FRZB\Component\RequestMapper\Data\ConverterData;
use FRZB\Component\RequestMapper\Data\ConverterType;
use FRZB\Component\RequestMapper\Locator\ConverterLocatorInterface as ConverterLocator;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(ConverterLocator::REQUEST_MAPPER_CONVERTERS_TAG)]
final class QueryConverter extends AbstractTypeConverter
{
    public static function getType(): string
    {
        return ConverterType::QUERY;
    }

    protected function getContent(ConverterData $data): array
    {
        return $data->getRequest()->query->all();
    }
}
