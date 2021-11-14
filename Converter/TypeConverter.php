<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Converter;

use FRZB\Component\RequestMapper\Data\ConverterData;
use FRZB\Component\RequestMapper\Data\ConverterType;
use FRZB\Component\RequestMapper\Exception\TypeConverterException;
use FRZB\Component\RequestMapper\Exception\ValidationException;

interface TypeConverter
{
    /**
     * Converts request data to object.
     *
     * @throws TypeConverterException
     * @throws ValidationException
     */
    public function convert(ConverterData $data): object;

    /** returns type of converter @see ConverterType. */
    public static function getType(): string;
}
