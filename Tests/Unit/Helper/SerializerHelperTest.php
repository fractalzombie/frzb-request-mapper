<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Helper;

use FRZB\Component\RequestMapper\Helper\SerializerHelper;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserWithSerializedNameRequest;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
class SerializerHelperTest extends TestCase
{
    /** @dataProvider caseProvider */
    public function testGetSerializedNameAttributeMethod(array $properties, array $expectedPropertyNames): void
    {
        foreach ($properties as $index => $property) {
            $expectedPropertyName = $expectedPropertyNames[$index];
            $serializedPropertyName = SerializerHelper::getSerializedNameAttribute($property)->getSerializedName();
            self::assertSame($expectedPropertyName, $serializedPropertyName);
        }
    }

    public function caseProvider(): iterable
    {
        yield sprintf('with "%s"', CreateUserWithSerializedNameRequest::class) => [
            'properties' => (new \ReflectionClass(CreateUserWithSerializedNameRequest::class))->getProperties(),
            'expected_properties' => ['name', 'uuid', 'amountOfWallet'],
        ];

        yield sprintf('with "%s"', CreateUserRequest::class) => [
            'class' => (new \ReflectionClass(CreateUserRequest::class))->getProperties(),
            'expected_properties' => ['name', 'userId', 'amount'],
        ];
    }
}
