<?php

declare(strict_types=1);

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *
 * Copyright (c) 2023 Mykhailo Shtanko fractalzombie@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE.MD
 * file that was distributed with this source code.
 */

namespace FRZB\Component\RequestMapper\Tests\Unit\Extractor;

use FRZB\Component\PhpDocReader\Reader\ReaderService;
use FRZB\Component\PhpDocReader\Resolver\ResolverService;
use FRZB\Component\RequestMapper\ClassMapper\ClassMapper;
use FRZB\Component\RequestMapper\Extractor\ParametersExtractor;
use FRZB\Component\RequestMapper\PropertyMapper\Mapper\ArrayAsAttributePropertyMapper;
use FRZB\Component\RequestMapper\PropertyMapper\Mapper\BuiltinPropertyMapper;
use FRZB\Component\RequestMapper\PropertyMapper\Mapper\DefaultPropertyMapper;
use FRZB\Component\RequestMapper\PropertyMapper\Mapper\DocBlockArrayPropertyMapper;
use FRZB\Component\RequestMapper\PropertyMapper\PropertyMapperLocator;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateNestedUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserWithSerializedNameRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestRequest;
use FRZB\Component\RequestMapper\TypeExtractor\Extractor\ArrayTypeAttributeExtractor;
use FRZB\Component\RequestMapper\TypeExtractor\Extractor\DocBlockTypeExtractor;
use FRZB\Component\RequestMapper\TypeExtractor\TypeExtractorLocator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/** @internal */
#[Group('request-mapper')]
class ParametersExtractorTest extends TestCase
{
    private ParametersExtractor $parameterExtractor;

    protected function setUp(): void
    {
        $readerService = new ReaderService(new ResolverService());
        $extractors = [$docBlockExtractor = new DocBlockTypeExtractor($readerService), $attributeExtractor = new ArrayTypeAttributeExtractor()];
        $extractorLocator = new TypeExtractorLocator($extractors);
        $mappers = [new ArrayAsAttributePropertyMapper($attributeExtractor), new DocBlockArrayPropertyMapper($docBlockExtractor), new BuiltinPropertyMapper($extractorLocator), new DefaultPropertyMapper()];
        $mapperLocator = new PropertyMapperLocator($mappers);
        $classMapper = new ClassMapper($mapperLocator);

        $this->parameterExtractor = new ParametersExtractor($classMapper);
    }

    #[DataProvider('caseProvider')]
    public function testExtractMethod(string $class, array $parameters): void
    {
        self::assertSame($parameters, $this->parameterExtractor->extract($class, $parameters));
    }

    public static function caseProvider(): iterable
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
            'parameters' => [...$parameters, ...['userId' => null, 'amount' => null]],
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
            'parameters' => [...$parameters, ...['userId' => TestConstant::USER_ID, 'amount' => TestConstant::USER_AMOUNT]],
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
