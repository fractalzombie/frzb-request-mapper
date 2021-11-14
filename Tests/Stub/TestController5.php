<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Data\ConverterType;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestController5
{
    #[ParamConverter('dto', ConverterType::QUERY, TestRequest::class)]
    public function method(TestRequest $dto): JsonResponse
    {
        try {
            return new JsonResponse(json_encode($dto, \JSON_THROW_ON_ERROR));
        } catch (\JsonException) {
            return new JsonResponse([]);
        }
    }
}
