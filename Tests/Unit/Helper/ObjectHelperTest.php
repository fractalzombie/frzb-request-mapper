<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Helper;

use FRZB\Component\RequestMapper\Helper\ObjectHelper;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
class ObjectHelperTest extends TestCase
{
    /** @dataProvider caseProvider */
    public function testIsArrayHasAllPropertiesFromClass(array $properties, string $class, bool $expected): void
    {
        self::assertSame($expected, ObjectHelper::isArrayHasAllPropertiesFromClass($properties, $class));
    }

    public function caseProvider(): iterable
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
