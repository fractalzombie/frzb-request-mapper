<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub\Controller;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @internal
 */
class TestWithoutParameterNameAndParameterClassController
{
    #[ParamConverter]
    public function method(TestRequest $dto): JsonResponse
    {
        try {
            return new JsonResponse(json_encode($dto, \JSON_THROW_ON_ERROR));
        } catch (\JsonException) {
            return new JsonResponse([]);
        }
    }
}
