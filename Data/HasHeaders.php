<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

interface HasHeaders
{
    public function setHeaders(array $headers): void;

    public function getHeaders(): array;
}
