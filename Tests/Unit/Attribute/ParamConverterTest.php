<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Attribute;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\TestController;
use FRZB\Component\RequestMapper\Tests\Stub\TestRequest;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
class ParamConverterTest extends TestCase
{
    public function testGetValidationGroupsMethod(): void
    {
        $converterWithDefaultGroup = new ParamConverter(validationGroups: [CreateUserRequest::class]);
        $converterWithoutDefaultGroup = new ParamConverter(validationGroups: [CreateUserRequest::class], useDefaultValidationGroup: false);

        self::assertNotSame($converterWithDefaultGroup->getValidationGroups(), $converterWithoutDefaultGroup->getValidationGroups());
        self::assertSame($converterWithDefaultGroup->getValidationGroups(), [CreateUserRequest::class, ParamConverter::DEFAULT_VALIDATION_GROUP]);
        self::assertSame($converterWithoutDefaultGroup->getValidationGroups(), [CreateUserRequest::class]);
    }

    /** @dataProvider equalsCaseProvider */
    public function testEqualsMethod(ParamConverter $first, object $second, bool $expected): void
    {
        self::assertSame($expected, $first->equals($second));
    }

    public function equalsCaseProvider(): iterable
    {
        yield 'with equals parameter class' => [
            'first' => new ParamConverter(CreateUserRequest::class),
            'second' => new ParamConverter(CreateUserRequest::class),
            'expected' => true,
        ];

        yield 'with equals parameter class and equals parameter name' => [
            'first' => new ParamConverter(CreateUserRequest::class, 'request'),
            'second' => new ParamConverter(CreateUserRequest::class, 'request'),
            'expected' => true,
        ];

        yield 'with not equals parameter class and equals parameter name' => [
            'first' => new ParamConverter(CreateUserRequest::class, 'request'),
            'second' => new ParamConverter(TestRequest::class, 'request'),
            'expected' => false,
        ];

        yield 'with equals parameter class and not equals parameter name' => [
            'first' => new ParamConverter(CreateUserRequest::class, 'first'),
            'second' => new ParamConverter(CreateUserRequest::class, 'second'),
            'expected' => false,
        ];

        yield 'with equals parameter class and reflection parameter' => [
            'first' => new ParamConverter(TestRequest::class, 'dto'),
            'second' => (new \ReflectionClass(TestController::class))->getMethod('method')?->getParameters()[0],
            'expected' => true,
        ];
    }
}
