<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Data;

use FRZB\Component\RequestMapper\Data\ValidationError;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
class ValidationErrorTest extends TestCase
{
    public function testToStringMethod(): void
    {
        self::assertSame(
            'type: "FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest", field: "[userId]", message: "This is not a valid UUID."',
            (string) new ValidationError(CreateUserRequest::class, '[userId]', 'This is not a valid UUID.'),
        );
    }
}
