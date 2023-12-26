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

namespace FRZB\Component\RequestMapper\Tests\Unit\Helper;

use FRZB\Component\RequestMapper\Helper\SerializerHelper;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserWithSerializedNameRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('request-mapper')]
class SerializerHelperTest extends TestCase
{
    #[DataProvider('caseProvider')]
    public function testGetSerializedNameAttributeMethod(array $properties, array $expectedPropertyNames): void
    {
        foreach ($properties as $index => $property) {
            $expectedPropertyName = $expectedPropertyNames[$index];
            $serializedPropertyName = SerializerHelper::getSerializedNameAttribute($property)->getSerializedName();
            self::assertSame($expectedPropertyName, $serializedPropertyName);
        }
    }

    public static function caseProvider(): iterable
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
