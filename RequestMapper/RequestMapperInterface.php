<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\RequestMapper;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use FRZB\Component\RequestMapper\Attribute\RequestBody;
use FRZB\Component\RequestMapper\Exception\ConverterException;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

#[AsAlias(RequestMapper::class)]
interface RequestMapperInterface
{
    /**
     * Converts request data to object.
     *
     * @throws ConverterException
     * @throws ValidationException
     */
    public function map(Request $request, RequestBody $attribute): object;
}
