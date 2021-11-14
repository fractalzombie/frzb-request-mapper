<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Data\ConverterType;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestController6
{
    #[ParamConverter('dto', ConverterType::QUERY, TestRequest::class)]
    public function __invoke(TestRequest $dto): JsonResponse
    {
        try {
            return new JsonResponse(json_encode($dto, \JSON_THROW_ON_ERROR));
        } catch (\JsonException) {
            return new JsonResponse([]);
        }
    }
}
