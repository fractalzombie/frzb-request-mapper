<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Extractor;

use FRZB\Component\PhpDocReader\Reader\ReaderService;
use FRZB\Component\PhpDocReader\Resolver\ResolverService;
use FRZB\Component\RequestMapper\Extractor\ParametersExtractor;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateNestedUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserWithSerializedNameRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('request-mapper')]
class ParametersExtractorTest extends TestCase
{
    #[DataProvider('caseProvider')]
    public function testExtractMethod(string $class, array $parameters): void
    {
        self::assertSame($parameters, (new ParametersExtractor(new ReaderService(new ResolverService())))->extract($class, $parameters));
    }

    public function caseProvider(): iterable
    {
        $parameters = [
            'name' => TestConstant::USER_NAME,
            'userId' => TestConstant::USER_ID,
            'amount' => TestConstant::USER_AMOUNT,
        ];

        yield sprintf('"%s" with parameters: "%s"', CreateUserRequest::class, implode(', ', array_keys($parameters))) => [
            'class' => CreateUserRequest::class,
            'parameters' => $parameters,
        ];

        $parameters = [
            'name' => TestConstant::USER_NAME,
        ];

        yield sprintf('"%s" with parameters: "%s"', CreateUserRequest::class, implode(', ', array_keys($parameters))) => [
            'class' => CreateUserRequest::class,
            'parameters' => array_merge($parameters, ['userId' => null, 'amount' => null]),
        ];

        $parameters = [
            'name' => TestConstant::UUID,
            'model' => TestConstant::USER_NAME,
        ];

        yield sprintf('"%s" with parameters: "%s"', TestRequest::class, implode(', ', array_keys($parameters))) => [
            'class' => TestRequest::class,
            'parameters' => $parameters,
        ];

        $parameters = [
            'name' => TestConstant::USER_NAME,
            'uuid' => TestConstant::USER_ID,
            'amountOfWallet' => TestConstant::USER_AMOUNT,
        ];

        yield sprintf('"%s" with parameters: "%s"', CreateUserWithSerializedNameRequest::class, implode(', ', array_keys($parameters))) => [
            'class' => CreateUserRequest::class,
            'parameters' => array_merge($parameters, ['userId' => TestConstant::USER_ID, 'amount' => TestConstant::USER_AMOUNT]),
        ];

        $parameters = [
            'name' => TestConstant::USER_NAME,
            'request' => [
                'name' => TestConstant::USER_NAME,
                'userId' => TestConstant::USER_ID,
                'amount' => TestConstant::USER_AMOUNT,
            ],
        ];

        yield sprintf('"%s" with parameters: "%s"', CreateNestedUserRequest::class, implode(', ', array_keys($parameters))) => [
            'class' => CreateNestedUserRequest::class,
            'parameters' => $parameters,
        ];
    }
}
