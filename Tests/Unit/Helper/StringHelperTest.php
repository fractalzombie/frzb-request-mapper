<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Helper;

use FRZB\Component\RequestMapper\Helper\StringHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('request-mapper')]
class StringHelperTest extends TestCase
{
    #[DataProvider('normalizeProvider')]
    public function testNormalizePrefix(string $prefix, string $expectedValue): void
    {
        self::assertSame($expectedValue, StringHelper::normalize($prefix));
    }

    public function normalizeProvider(): iterable
    {
        return [
            ['some-domain.local', 'some-domain-local'],
            ['some%domain%local', 'some-domain-local'],
            ['some$domain$local', 'some-domain-local'],
            ['test-app.dev-tech.sub-domain.some-domain.cloud', 'test-app-dev-tech-sub-domain-some-domain-cloud'],
        ];
    }

    #[DataProvider('prefixProvider')]
    public function testMakePrefixMethod(string $prefix, string $expectedValue, ?string $value = null, ?string $delimiter = null): void
    {
        self::assertSame($expectedValue, StringHelper::makePrefix($prefix, $value, $delimiter));
    }

    public function prefixProvider(): iterable
    {
        return [
            ['some-domain.local', 'some-domain-local', null, '-'],
            ['some%domain%local', 'some-domain-local', null, '-'],
            ['some$domain$local', 'some-domain-local', null, '-'],
            ['test-app.dev-tech.sub-domain.some-domain.cloud', 'test-app-dev-tech-sub-domain-some-domain-cloud', null, '-'],
            ['some-domain.local', 'some-domain-local_prefix', 'prefix', '_'],
            ['some-domain.local', 'some-domain-local.prefix', 'prefix', '.'],
        ];
    }

    #[DataProvider('snakeCaseProvider')]
    public function testToSnakeCaseMethod(string $sourceValue, string $expectedValue): void
    {
        self::assertSame($expectedValue, StringHelper::toSnakeCase($sourceValue));
    }

    public function snakeCaseProvider(): iterable
    {
        return [
            ['SomeTrueValue', 'some_true_value'],
            ['SomeValue', 'some_value'],
            ['someValue', 'some_value'],
        ];
    }

    #[DataProvider('snakeCaseToCamelCaseProvider')]
    public function testToCamelCaseMethod(string $sourceValue, string $expectedValue): void
    {
        self::assertSame($expectedValue, StringHelper::toCamelCase($sourceValue));
    }

    public function snakeCaseToCamelCaseProvider(): iterable
    {
        return [
            ['camel_case_name', 'CamelCaseName'],
            ['camel_case', 'CamelCase'],
            ['camel', 'Camel'],
            ['camel-case', 'CamelCase'],
            ['camel-case-name', 'CamelCaseName'],
        ];
    }

    #[DataProvider('snakeCaseToLowerCamelCaseProvider')]
    public function testToLowerCamelCaseMethod(string $sourceValue, string $expectedValue): void
    {
        self::assertSame($expectedValue, StringHelper::toLowerCamelCase($sourceValue));
    }

    public function snakeCaseToLowerCamelCaseProvider(): iterable
    {
        return [
            ['camel_case_name', 'camelCaseName'],
            ['camel_case', 'camelCase'],
            ['camel', 'camel'],
            ['camel-case', 'camelCase'],
            ['camel-case-name', 'camelCaseName'],
        ];
    }

    #[DataProvider('kebabCaseProvider')]
    public function testToKebabCaseMethod(string $sourceValue, string $expectedValue): void
    {
        self::assertSame($expectedValue, StringHelper::toKebabCase($sourceValue));
    }

    public function kebabCaseProvider(): iterable
    {
        return [
            ['SomeTrueValue', 'some-true-value'],
            ['SomeValue', 'some-value'],
            ['someValue', 'some-value'],
        ];
    }

    #[DataProvider('successSubValuesProvider')]
    public function testContainsMethod(string $sourceValue, string $subValue, bool $expected): void
    {
        self::assertSame($expected, StringHelper::contains($sourceValue, $subValue));
    }

    public function successSubValuesProvider(): iterable
    {
        return [
            ['SomeTrueValue', 'True', true],
            ['Hello my kitty', 'my', true],
            ['Bye bye', 'ye', true],
            ['SomeTrueValue', 'False', false],
            ['Hello my kitty', 'notmy', false],
            ['Bye bye', 'hello', false],
        ];
    }

    #[DataProvider('bracketsProvider')]
    public function testRemoveBracketsMethod(string $inputValue, string $expectedValue, array $brackets): void
    {
        self::assertEquals($expectedValue, StringHelper::removeBrackets($inputValue, $brackets));
    }

    public function bracketsProvider(): iterable
    {
        yield 'standard brackets' => [
            '[hello_world]',
            'hello_world',
            ['[', ']'],
        ];

        yield 'not standard brackets' => [
            '{hello_world}',
            'hello_world',
            ['{', '}'],
        ];
    }
}
