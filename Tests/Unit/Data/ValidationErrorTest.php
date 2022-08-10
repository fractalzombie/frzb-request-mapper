<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Data;

use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use FRZB\Component\RequestMapper\ValueObject\ValidationError;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/** @internal */
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
