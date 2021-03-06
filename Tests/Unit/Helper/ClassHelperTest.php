<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Helper;

use FRZB\Component\RequestMapper\Helper\ClassHelper;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use FRZB\Component\RequestMapper\Tests\Stub\Enum\TestEnum;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
class ClassHelperTest extends TestCase
{
    /** @dataProvider isNotBuiltinCaseProvider */
    public function testIsNotBuiltinAndExistsMethod(string $class, bool $isNotBuiltinAndExists): void
    {
        self::assertSame($isNotBuiltinAndExists, ClassHelper::isNotBuiltinAndExists($class));
    }

    public function isNotBuiltinCaseProvider(): iterable
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

    /** @dataProvider getShortNameCaseProvider */
    public function testGetShortNameMethod(string $className, string $expectedName): void
    {
        self::assertSame($expectedName, ClassHelper::getShortName($className));
    }

    public function getShortNameCaseProvider(): iterable
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

    /** @dataProvider getMethodParametersCaseProvider */
    public function testGetMethodParametersMethod(string $className, string $method, array $expectedMapping): void
    {
        $mapping = array_map(
            static fn (\ReflectionParameter $p) => $p->getName(),
            ClassHelper::getMethodParameters($className, $method),
        );

        self::assertSame($expectedMapping, $mapping);
    }

    public function getMethodParametersCaseProvider(): iterable
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

    /** @dataProvider isEnumCaseProvider */
    public function testIsEnumMethod(string $className, bool $expectedResult): void
    {
        self::assertSame(ClassHelper::isEnum($className), $expectedResult);
    }

    public function isEnumCaseProvider(): iterable
    {
        yield sprintf('enum "%s" is valid', TestEnum::class) => [
            'value' => TestEnum::class,
            'expected_result' => true,
        ];

        yield sprintf('class "%s" is invalid', 'NoClass') => [
            'value' => 'NoClass',
            'expected_result' => false,
        ];
    }

    /** @dataProvider isArrayHasAllPropertiesFromClassCaseProvider */
    public function testIsArrayHasAllPropertiesFromClass(array $properties, string $class, bool $expected): void
    {
        self::assertSame($expected, ClassHelper::isArrayHasAllPropertiesFromClass($properties, $class));
    }

    public function isArrayHasAllPropertiesFromClassCaseProvider(): iterable
    {
        $properties = ['name' => TestConstant::USER_NAME, 'userId' => TestConstant::UUID, 'amount' => TestConstant::USER_AMOUNT];

        yield sprintf('Class %s and properties %s', CreateUserRequest::class, implode(', ', array_keys($properties))) => [
            'properties' => $properties,
            'class' => CreateUserRequest::class,
            'expected' => true,
        ];

        $properties = ['name' => TestConstant::USER_NAME, 'userId' => TestConstant::UUID];

        yield sprintf('Class %s and properties %s', CreateUserRequest::class, implode(', ', array_keys($properties))) => [
            'properties' => $properties,
            'class' => CreateUserRequest::class,
            'expected' => false,
        ];

        yield sprintf('Class %s and properties %s', 'NoClass', 'empty') => [
            'properties' => [],
            'class' => 'NoClass',
            'expected' => false,
        ];
    }
}
