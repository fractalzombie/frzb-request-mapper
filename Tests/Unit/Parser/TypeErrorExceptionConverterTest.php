<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Parser;

use FRZB\Component\RequestMapper\Parser\TypeErrorExceptionConverter;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestWithoutParametersRequest;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
final class TypeErrorExceptionConverterTest extends TestCase
{
    private const TYPE_ERROR_MESSAGE_TEMPLATE = 'Invalid parameter "%s" type, expected "%s", proposed "%s"';
    private const ARGUMENT_ERROR_MESSAGE_TEMPLATE = 'Argument with position "%s" not exists';

    /** @dataProvider caseProvider */
    public function testConvert(
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
        $exception = new \TypeError(sprintf($template, $class, $expected, $proposed));
        $message = sprintf(self::TYPE_ERROR_MESSAGE_TEMPLATE, $parameter, $expected, $proposed);

        $error = (new TypeErrorExceptionConverter())->convert($exception, $data);

        self::assertSame($parameter, $error->getField());
        self::assertSame($message, $error->getMessage());
    }

    public function caseProvider(): iterable
    {
        yield 'test it with type of template' => [
            'parameter' => 'name',
            'class' => TestRequest::class,
            'expected' => 'string',
            'proposed' => 'array',
            'template' => 'Argument 1 passed to %s::__construct() must be of the type %s, %s given, called in /some/path/SomeFile.php on line 16 and defined in /some/path/SomeFile.php on line 20',
        ];

        yield 'test it with instance of template' => [
            'parameter' => 'name',
            'class' => TestRequest::class,
            'expected' => 'string',
            'proposed' => 'array',
            'template' => 'Argument 1 passed to %s::__construct() must be an instance of %s, instance of %s given, called in /some/path/SomeFile.php on line 16 and defined in /some/path/SomeFile.php on line 20',
        ];

        yield 'test it with implementation template' => [
            'parameter' => 'name',
            'class' => TestRequest::class,
            'expected' => 'string',
            'proposed' => 'array',
            'template' => 'Argument 1 passed to %s::__construct() must implement interface %s, instance of %s given, called in /some/path/SomeFile.php on line 16 and defined in /some/path/SomeFile.php on line 20',
        ];

        yield 'test it with bad parameter' => [
            'parameter' => 'name',
            'class' => TestWithoutParametersRequest::class,
            'expected' => 'string',
            'proposed' => 'array',
            'template' => 'Argument 1 passed to %s::__construct() must be of the type %s, %s given, called in /some/path/SomeFile.php on line 16 and defined in /some/path/SomeFile.php on line 20',
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
