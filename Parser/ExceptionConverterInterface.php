<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Parser;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use FRZB\Component\RequestMapper\Data\ErrorInterface as Error;

#[AsAlias(service: TypeErrorExceptionConverter::class)]
interface ExceptionConverterInterface
{
    /**
     * @throws \TypeError
     * @throws \InvalidArgumentException
     */
    public function convert(\TypeError $e, array $data): Error;
}
