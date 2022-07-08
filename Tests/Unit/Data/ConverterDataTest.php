<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Data;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Data\Context;
use FRZB\Component\RequestMapper\Tests\Helper\RequestHelper;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

/**
 * @group request-mapper
 *
 * @internal
 */
class ConverterDataTest extends TestCase
{
    public function testConverterDataMethods(): void
    {
        $params = ['name' => TestConstant::USER_NAME];
        $request = RequestHelper::makeRequest(Request::METHOD_POST, $params);
        $attribute = new ParamConverter(CreateUserRequest::class, 'request');
        $data = new Context($request, $attribute);

        self::assertSame($request, $data->getRequest());
        self::assertSame($params, $data->getRequestParameters());
        self::assertSame(CreateUserRequest::class, $data->getParameterClass());
        self::assertSame([AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true], $data->getSerializerContext());
        self::assertSame(['Default'], $data->getValidationGroups());
        self::assertTrue($data->isValidationNeeded());
    }
}
