<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Data;

use FRZB\Component\RequestMapper\Data\TypeError;
use FRZB\Component\RequestMapper\Exception\TypeErrorInvalidArgumentException;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
final class TypeErrorTest extends TestCase
{
    /** @dataProvider caseProvider */
    public function testCreation(array $params, bool $throws = false): void
    {
        if ($throws) {
            $message = sprintf('Params have not needed values "%s"', implode(', ', array_keys($params)));
            $this->expectException(TypeErrorInvalidArgumentException::class);
            $this->expectExceptionMessage($message);
        }

        $error = TypeError::fromArray($params);

        self::assertSame($params['class'], $error->getClass());
        self::assertSame($params['method'], $error->getMethod());
        self::assertSame($params['position'], $error->getPosition());
        self::assertSame($params['expected'], $error->getExpected());
        self::assertSame($params['proposed'], $error->getProposed());
    }

    public function caseProvider(): iterable
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
