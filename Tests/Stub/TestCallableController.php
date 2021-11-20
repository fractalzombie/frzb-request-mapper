<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @internal
 */
class TestCallableController
{
    #[ParamConverter(class: TestRequest::class, name: 'dto')]
    public function __invoke(TestRequest $dto): JsonResponse
    {
        return new JsonResponse($dto);
    }
}
