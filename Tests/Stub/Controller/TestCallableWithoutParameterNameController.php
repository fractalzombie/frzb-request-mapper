<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub\Controller;

use FRZB\Component\RequestMapper\Attribute\RequestBody;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

/** @internal */
class TestCallableWithoutParameterNameController
{
    #[RequestBody(requestClass: TestRequest::class)]
    public function __invoke(TestRequest $dto): JsonResponse
    {
        return new JsonResponse($dto);
    }
}
