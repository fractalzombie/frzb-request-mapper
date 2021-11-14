<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Data\ConverterType;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestController
{
    #[ParamConverter('dto', ConverterType::REQUEST, TestRequest::class)]
    public function __invoke(TestRequest $dto): JsonResponse
    {
        return new JsonResponse($dto);
    }
}
