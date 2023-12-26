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

namespace FRZB\Component\RequestMapper\Tests\Unit\Attribute;

use FRZB\Component\RequestMapper\Attribute\RequestBody;
use FRZB\Component\RequestMapper\Tests\Stub\Controller\TestController;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('request-mapper')]
class RequestBodyAttributeTest extends TestCase
{
    public function testGetValidationGroupsMethod(): void
    {
        $converterWithDefaultGroup = new RequestBody(validationGroups: [CreateUserRequest::class]);
        $converterWithoutDefaultGroup = new RequestBody(validationGroups: [CreateUserRequest::class], useDefaultValidationGroup: false);

        self::assertNotSame($converterWithDefaultGroup->validationGroups, $converterWithoutDefaultGroup->validationGroups);
        self::assertSame([CreateUserRequest::class, ...RequestBody::DEFAULT_VALIDATION_GROUPS], $converterWithDefaultGroup->validationGroups);
        self::assertSame([CreateUserRequest::class], $converterWithoutDefaultGroup->validationGroups);
    }

    #[DataProvider('equalsCaseProvider')]
    public function testEqualsMethod(RequestBody $first, object $second, bool $expected): void
    {
        self::assertSame($expected, $first->equals($second));
    }

    public static function equalsCaseProvider(): iterable
    {
        yield 'with equals parameter class' => [
            'first' => new RequestBody(CreateUserRequest::class),
            'second' => new RequestBody(CreateUserRequest::class),
            'expected' => true,
        ];

        yield 'with equals parameter class and equals parameter name' => [
            'first' => new RequestBody(CreateUserRequest::class, 'request'),
            'second' => new RequestBody(CreateUserRequest::class, 'request'),
            'expected' => true,
        ];

        yield 'with not equals parameter class and equals parameter name' => [
            'first' => new RequestBody(CreateUserRequest::class, 'request'),
            'second' => new RequestBody(TestRequest::class, 'request'),
            'expected' => false,
        ];

        yield 'with equals parameter class and not equals parameter name' => [
            'first' => new RequestBody(CreateUserRequest::class, 'first'),
            'second' => new RequestBody(CreateUserRequest::class, 'second'),
            'expected' => false,
        ];

        yield 'with equals parameter class and reflection parameter' => [
            'first' => new RequestBody(TestRequest::class, 'dto'),
            'second' => (new \ReflectionClass(TestController::class))->getMethod('method')?->getParameters()[0],
            'expected' => true,
        ];
    }
}
