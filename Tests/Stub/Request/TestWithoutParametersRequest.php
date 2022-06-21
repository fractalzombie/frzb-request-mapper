<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub\Request;

/**
 * @internal
 */
class TestWithoutParametersRequest
{
    public string $name;
    public string $model;

    public function __construct()
    {
        $this->name = 'testName';
        $this->model = 'testModel';
    }
}
