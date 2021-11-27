<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Helper;

use FRZB\Component\RequestMapper\Helper\ClassHelper;
use FRZB\Component\RequestMapper\Tests\Stub\CreateNestedUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequestWithSerializedName;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
class ClassHelperTest extends TestCase
{
    /** @dataProvider builtinCaseProvider */
    public function testIsNotBuiltinAndExistsMethod(string $class, bool $isNotBuiltinAndExists): void
    {
        self::assertSame($isNotBuiltinAndExists, ClassHelper::isNotBuiltinAndExists($class));
    }

    public function builtinCaseProvider(): iterable
    {
        yield sprintf('with "%s"', CreateUserRequest::class) => [
            'class' => CreateUserRequest::class,
            'is_not_builtin_and_exists' => true,
        ];

        yield sprintf('with "%s"', \DateTimeImmutable::class) => [
            'class' => \DateTimeImmutable::class,
            'is_not_builtin_and_exists' => false,
        ];

        yield sprintf('with "%s"', 'string') => [
            'class' => 'string',
            'is_not_builtin_and_exists' => false,
        ];
    }

    /** @dataProvider shortNameCaseProvider */
    public function testGetShortNameMethod(string $className, string $expectedName): void
    {
        self::assertSame($expectedName, ClassHelper::getShortName($className));
    }

    public function shortNameCaseProvider(): iterable
    {
        yield sprintf('class "%s"', CreateUserRequest::class) => [
            'class_name' => CreateUserRequest::class,
            'expected_name' => 'CreateUserRequest',
        ];

        yield sprintf('class "%s"', \DateTimeImmutable::class) => [
            'class_name' => \DateTimeImmutable::class,
            'expected_name' => 'DateTimeImmutable',
        ];

        yield sprintf('class "%s"', 'string') => [
            'class_name' => 'string',
            'expected_name' => 'string',
        ];
    }

    /** @dataProvider nameContainsCaseProvider */
    public function testIsNameContainsMethod(string $className, array $haystack, bool $contains): void
    {
        self::assertSame($contains, ClassHelper::isNameContains($className, ...$haystack));
    }

    public function nameContainsCaseProvider(): iterable
    {
        yield sprintf('class "%s" and haystack "%s"', CreateUserRequest::class, implode(', ', ['Request'])) => [
            'class_name' => CreateUserRequest::class,
            'haystack' => ['Request'],
            'contains' => true,
        ];

        yield sprintf('class "%s" and haystack "%s"', CreateUserRequest::class, implode(', ', ['DTO'])) => [
            'class_name' => CreateUserRequest::class,
            'haystack' => ['DTO'],
            'contains' => false,
        ];

        yield sprintf('class "%s" and haystack "%s"', \DateTimeImmutable::class, implode(', ', ['Time', 'Immutable'])) => [
            'class_name' => \DateTimeImmutable::class,
            'haystack' => ['Time', 'Immutable'],
            'contains' => true,
        ];

        yield sprintf('class "%s" and haystack "%s"', 'string', implode(', ', ['Request', 'DTO', 'Dto'])) => [
            'class_name' => 'string',
            'haystack' => ['Request', 'DTO', 'Dto'],
            'contains' => false,
        ];
    }

    /** @dataProvider propertyMappingCaseProvider */
    public function testGetPropertyMappingMethod(string $className, array $expectedMapping): void
    {
        self::assertSame($expectedMapping, ClassHelper::getPropertyMapping($className));
    }

    public function propertyMappingCaseProvider(): iterable
    {
        yield sprintf('class "%s", mapping "%s"', CreateUserRequest::class, implode(', ', ['name', 'userId', 'amount'])) => [
            'class_name' => CreateUserRequest::class,
            'expected_mapping' => ['name' => 'string', 'userId' => 'string', 'amount' => 'float'],
        ];

        yield sprintf('class "%s", mapping "%s"', CreateUserRequestWithSerializedName::class, implode(', ', ['name', 'uuid', 'amountOfWallet'])) => [
            'class_name' => CreateUserRequestWithSerializedName::class,
            'expected_mapping' => ['name' => 'string', 'uuid' => 'string', 'amountOfWallet' => 'float'],
        ];

        yield sprintf('class "%s", mapping "%s"', CreateNestedUserRequest::class, implode(', ', ['name', 'request'])) => [
            'class_name' => CreateNestedUserRequest::class,
            'expected_mapping' => ['name' => 'string', 'request' => CreateUserRequest::class],
        ];

        yield sprintf('class "%s", mapping "%s"', 'NoClass', implode(', ', [])) => [
            'class_name' => 'NoClass',
            'expected_mapping' => [],
        ];
    }

    /** @dataProvider methodParametersCaseProvider */
    public function testGetMethodParametersMethod(string $className, string $method, array $expectedMapping): void
    {
        $mapping = array_map(
            static fn (\ReflectionParameter $p) => $p->getName(),
            ClassHelper::getMethodParameters($className, $method),
        );

        self::assertSame($expectedMapping, $mapping);
    }

    public function methodParametersCaseProvider(): iterable
    {
        yield sprintf('class "%s", mapping "%s"', CreateUserRequest::class, implode(', ', ['name', 'userId', 'amount'])) => [
            'class_name' => CreateUserRequest::class,
            'method' => '__construct',
            'expected_mapping' => ['name', 'userId', 'amount'],
        ];

        yield sprintf('class "%s", mapping "%s"', 'NoClass', implode(', ', [])) => [
            'class_name' => 'NoClass',
            'method' => 'no_method',
            'expected_mapping' => [],
        ];
    }
}
