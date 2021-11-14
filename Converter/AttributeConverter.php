<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Converter;

use FRZB\Component\RequestMapper\Data\ConverterData;
use FRZB\Component\RequestMapper\Data\ConverterType;
use FRZB\Component\RequestMapper\Locator\ConverterLocatorInterface as ConverterLocator;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(ConverterLocator::REQUEST_MAPPER_CONVERTERS_TAG)]
final class AttributeConverter extends AbstractTypeConverter
{
    private const ROUTE_PARAMS_KEY = '_route_params';

    public static function getType(): string
    {
        return ConverterType::ATTRIBUTE;
    }

    protected function getContent(ConverterData $data): array
    {
        $request = $data->getRequest();

        return array_merge($request->request->all(), $request->attributes->get(self::ROUTE_PARAMS_KEY));
    }
}
