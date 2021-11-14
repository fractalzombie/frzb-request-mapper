<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Utils;

use FRZB\Component\RequestMapper\Utils\StringUtil;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
class StringUtilTest extends TestCase
{
    /**
     * Correctly is /[^a-zA-Z\\d_-]/.
     *
     * @dataProvider prefixProvider
     */
    public function testItNormalizeStringCorrectly(string $prefix, string $expectedValue): void
    {
        self::assertSame($expectedValue, StringUtil::normalize($prefix));
    }

    public function prefixProvider(): iterable
    {
        return [
            ['some-domain.local', 'some-domain-local'],
            ['some%domain%local', 'some-domain-local'],
            ['some$domain$local', 'some-domain-local'],
            ['test-app.dev-tech.sub-domain.some-domain.cloud', 'test-app-dev-tech-sub-domain-some-domain-cloud'],
        ];
    }

    /** @dataProvider snakeCaseProvider */
    public function testItCanNormalizeToSnakeCase(string $sourceValue, string $expectedValue): void
    {
        self::assertSame($expectedValue, StringUtil::toSnakeCase($sourceValue));
    }

    public function snakeCaseProvider(): iterable
    {
        return [
            ['SomeTrueValue', 'some_true_value'],
            ['SomeValue', 'some_value'],
            ['someValue', 'some_value'],
        ];
    }

    /** @dataProvider snakeCaseToCamelCaseProvider */
    public function testCanConvertSnakeCaseToCamelCase(string $sourceValue, string $expectedValue): void
    {
        self::assertSame($expectedValue, StringUtil::toCamelCase($sourceValue));
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

    /** @dataProvider snakeCaseToLowerCamelCaseProvider */
    public function testCanConvertSnakeCaseToLowerCamelCase(string $sourceValue, string $expectedValue): void
    {
        self::assertSame($expectedValue, StringUtil::toLowerCamelCase($sourceValue));
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

    /** @dataProvider kebabCaseProvider */
    public function testItCanNormalizeToKebabCase(string $sourceValue, string $expectedValue): void
    {
        self::assertSame($expectedValue, StringUtil::toKebabCase($sourceValue));
    }

    public function kebabCaseProvider(): iterable
    {
        return [
            ['SomeTrueValue', 'some-true-value'],
            ['SomeValue', 'some-value'],
            ['someValue', 'some-value'],
        ];
    }

    /** @dataProvider successSubValuesProvider */
    public function testItCanFindSubValueInValue(string $sourceValue, string $subValue): void
    {
        self::assertTrue(StringUtil::contains($sourceValue, $subValue));
    }

    public function successSubValuesProvider(): iterable
    {
        return [
            ['SomeTrueValue', 'True'],
            ['Hello my kitty', 'my'],
            ['Bye bye', 'ye'],
        ];
    }

    /** @dataProvider failureSubValuesProvider */
    public function testItCantFindSubValueInValue(string $sourceValue, string $subValue): void
    {
        self::assertFalse(StringUtil::contains($sourceValue, $subValue));
    }

    public function failureSubValuesProvider(): iterable
    {
        return [
            ['SomeTrueValue', 'False'],
            ['Hello my kitty', 'notmy'],
            ['Bye bye', 'hello'],
        ];
    }

    /** @dataProvider bracketsProvider */
    public function testRemoveBracketsMethod(string $inputValue, string $expectedValue, array $brackets): void
    {
        self::assertEquals($expectedValue, StringUtil::removeBrackets($inputValue, $brackets));
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
