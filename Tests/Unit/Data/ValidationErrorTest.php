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

use FRZB\Component\RequestMapper\Data\ValidationError;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('request-mapper')]
class ValidationErrorTest extends TestCase
{
    public function testToStringMethod(): void
    {
        self::assertSame(
            'type: "FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest", field: "[userId]", message: "This is not a valid UUID."',
            (string) new ValidationError(CreateUserRequest::class, '[userId]', 'This is not a valid UUID.'),
        );
    }
}
