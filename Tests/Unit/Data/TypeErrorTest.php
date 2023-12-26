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

namespace FRZB\Component\RequestMapper\Tests\Unit\Data;

use FRZB\Component\RequestMapper\Data\TypeError;
use FRZB\Component\RequestMapper\Exception\TypeErrorInvalidArgumentException;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('request-mapper')]
final class TypeErrorTest extends TestCase
{
    #[DataProvider('caseProvider')]
    public function testCreation(array $params, bool $throws = false): void
    {
        if ($throws) {
            $message = sprintf('Params have not needed values "%s"', implode(', ', array_keys($params)));
            $this->expectException(TypeErrorInvalidArgumentException::class);
            $this->expectExceptionMessage($message);
        }

        $error = TypeError::fromArray($params);

        self::assertSame($params['class'], $error->class);
        self::assertSame($params['method'], $error->method);
        self::assertSame($params['position'], $error->position);
        self::assertSame($params['expected'], $error->expected);
        self::assertSame($params['proposed'], $error->proposed);
    }

    public static function caseProvider(): iterable
    {
        yield 'all parameters are exists' => [
            'params' => [
                'class' => CreateUserRequest::class,
                'method' => '__construct()',
                'position' => 1,
                'expected' => 'string',
                'proposed' => 'int',
            ],
            'throws' => false,
        ];

        yield 'not all parameters are exists' => [
            'params' => [
                'class' => CreateUserRequest::class,
                'method' => '__construct()',
                'position' => 1,
                'expected' => 'string',
            ],
            'throws' => true,
        ];
    }
}
