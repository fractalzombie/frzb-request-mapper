<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Utils;

use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequestWithSerializedName;
use FRZB\Component\RequestMapper\Utils\SerializerUtil;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
class SerializerUtilTest extends TestCase
{
    /** @dataProvider caseProvider */
    public function testGetSerializedNameAttributeMethod(array $properties, array $expectedPropertyNames): void
    {
        foreach ($properties as $index => $property) {
            $expectedPropertyName = $expectedPropertyNames[$index];
            $serializedPropertyName = SerializerUtil::getSerializedNameAttribute($property)->getSerializedName();
            self::assertSame($expectedPropertyName, $serializedPropertyName);
        }
    }

    public function caseProvider(): iterable
    {
        yield sprintf('with "%s"', CreateUserRequestWithSerializedName::class) => [
            'properties' => (new \ReflectionClass(CreateUserRequestWithSerializedName::class))->getProperties(),
            'expected_properties' => ['name', 'uuid', 'amountOfWallet'],
        ];

        yield sprintf('with "%s"', CreateUserRequest::class) => [
            'class' => (new \ReflectionClass(CreateUserRequest::class))->getProperties(),
            'expected_properties' => ['name', 'userId', 'amount'],
        ];
    }
}
