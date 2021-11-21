<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Utils;

use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest;
use FRZB\Component\RequestMapper\Utils\ClassUtil;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
class ClassUtilTest extends TestCase
{
    /** @dataProvider builtinCaseProvider */
    public function testIsNotBuiltinAndExistsMethod(string $class, bool $isNotBuiltinAndExists): void
    {
        self::assertSame($isNotBuiltinAndExists, ClassUtil::isNotBuiltinAndExists($class));
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
        self::assertSame($expectedName, ClassUtil::getShortName($className));
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
        self::assertSame($contains, ClassUtil::isNameContains($className, ...$haystack));
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
}
