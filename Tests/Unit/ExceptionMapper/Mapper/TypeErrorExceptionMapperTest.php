<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\ExceptionMapper\Mapper;

use FRZB\Component\RequestMapper\ExceptionMapper\Mapper\TypeErrorExceptionMapper;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestWithoutParametersRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('request-mapper')]
final class TypeErrorExceptionMapperTest extends TestCase
{
    private const TYPE_ERROR_MESSAGE_TEMPLATE = 'Invalid parameter "[%s]" type, expected "%s", proposed "%s"';
    private const ARGUMENT_ERROR_MESSAGE_TEMPLATE = 'Argument with position "%s" not exists';

    #[DataProvider('caseProvider')]
    public function testMapping(
        string $parameter,
        string $class,
        string $expected,
        string $proposed,
        string $template,
        ?int $position = null,
        ?bool $throws = false
    ): void {
        if ($throws && !$position) {
            $this->expectException(\TypeError::class);
        }

        if ($throws && $position) {
            $message = sprintf(self::ARGUMENT_ERROR_MESSAGE_TEMPLATE, $position);
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage($message);
        }

        $data = [$parameter => 'string'];
        $exception = new \TypeError(sprintf($template, $class, $parameter, $expected, $proposed));
        $message = sprintf(self::TYPE_ERROR_MESSAGE_TEMPLATE, $parameter, $expected, $proposed);

        $error = (new TypeErrorExceptionMapper())($exception, $data);

        self::assertSame("[{$parameter}]", $error->getField());
        self::assertSame($message, $error->getMessage());
    }

    public function caseProvider(): iterable
    {
        yield 'test it with type of template' => [
            'parameter' => 'name',
            'class' => TestRequest::class,
            'expected' => 'string',
            'proposed' => 'array',
            'template' => '%s::__construct(): Argument #1 ($%s) must be of type %s, %s given, called in /some/path/SomeFile.php on line 16 and defined in /some/path/SomeFile.php on line 20',
        ];

        yield 'test it with instance of template' => [
            'parameter' => 'name',
            'class' => TestRequest::class,
            'expected' => 'string',
            'proposed' => 'array',
            'template' => '%s::__construct(): Argument #1 ($%s) must be of type %s, %s given, called in /some/path/SomeFile.php on line 16 and defined in /some/path/SomeFile.php on line 20',
        ];

        yield 'test it with implementation template' => [
            'parameter' => 'name',
            'class' => TestRequest::class,
            'expected' => 'string',
            'proposed' => 'array',
            'template' => '%s::__construct(): Argument #1 ($%s) must be of type %s, %s given, called in /some/path/SomeFile.php on line 16 and defined in /some/path/SomeFile.php on line 20',
        ];

        yield 'test it with bad parameter' => [
            'parameter' => 'name',
            'class' => TestWithoutParametersRequest::class,
            'expected' => 'string',
            'proposed' => 'array',
            'template' => '%s::__construct(): Argument #1 ($%s) must be of type %s, %s given, called in /some/path/SomeFile.php on line 16 and defined in /some/path/SomeFile.php on line 20',
            'position' => 1,
            'throws' => true,
        ];

        yield 'test it with not matched template' => [
            'parameter' => 'name',
            'class' => TestRequest::class,
            'expected' => 'string',
            'proposed' => 'array',
            'template' => 'Something goes wrong with %s, because %s and %s',
            'position' => null,
            'throws' => true,
        ];
    }
}
