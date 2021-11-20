<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub;

/**
 * @internal
 */
class TestRequest
{
    public function __construct(
        public string $name,
        public string $model
    ) {
    }
}
