<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Utils;

use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest;
use FRZB\Component\RequestMapper\Utils\ClassUtil;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
class ClassUtilTest extends TestCase
{
    /** @dataProvider caseProvider */
    public function testIsNotBuiltinAndExistsMethod(string $class, bool $isNotBuiltinAndExists): void
    {
        self::assertSame($isNotBuiltinAndExists, ClassUtil::isNotBuiltinAndExists($class));
    }

    public function caseProvider(): iterable
    {
        yield sprintf('with "%s"', CreateUserRequest::class) => [
            'class' => CreateUserRequest::class,
            'is_not_builtin_and_exists' => true,
        ];

        yield sprintf('with "%s"', \DateTimeImmutable::class) => [
            'class' => \DateTimeImmutable::class,
            'is_not_builtin_and_exists' => false,
        ];

        yield sprintf('with "%s"', 'string') => [
            'class' => 'string',
            'is_not_builtin_and_exists' => false,
        ];
    }
}
