<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @internal
 */
class TestCallableControllerWithoutParameterName
{
    #[ParamConverter(class: TestRequest::class)]
    public function __invoke(TestRequest $dto): JsonResponse
    {
        return new JsonResponse($dto);
    }
}
