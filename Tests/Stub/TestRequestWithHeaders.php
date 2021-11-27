<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub;

use FRZB\Component\RequestMapper\Data\HasHeaders;

/**
 * @internal
 */
class TestRequestWithHeaders implements HasHeaders
{
    public function __construct(
        public string $name,
        public string $model,
        public array $headers = [],
    ) {
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
