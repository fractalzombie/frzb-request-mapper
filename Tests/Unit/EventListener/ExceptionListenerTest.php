<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\EventListener;

use FRZB\Component\RequestMapper\EventListener\ExceptionListener;
use FRZB\Component\RequestMapper\ExceptionFormatter\ExceptionFormatterInterface as ExceptionFormatter;
use FRZB\Component\RequestMapper\Helper\HeaderHelper;
use FRZB\Component\RequestMapper\Tests\Helper\RequestHelper;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use FRZB\Component\RequestMapper\ValueObject\FormattedError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/** @internal */
#[Group('request-mapper')]
#[RunClassInSeparateProcess]
class ExceptionListenerTest extends TestCase
{
    private ExceptionListener $listener;
    private EventDispatcher $eventDispatcher;
    private ExceptionFormatter $exceptionFormatter;

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
        $this->exceptionFormatter = $this->createMock(ExceptionFormatter::class);
        $this->listener = new ExceptionListener($this->exceptionFormatter, $this->eventDispatcher);
    }

    #[DataProvider('caseProvider')]
    public function testOnKernelRequestMethod(array $headers, InvocationOrder $expectFormatter, InvocationOrder $expectsDispatcher): void
    {
        $request = RequestHelper::makeRequest(Request::METHOD_POST, [], $headers);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $exception = new \TypeError(TestConstant::EXCEPTION_MESSAGE);
        $contractError = new FormattedError(TestConstant::EXCEPTION_MESSAGE, Response::HTTP_INTERNAL_SERVER_ERROR);

        $this->exceptionFormatter->expects($expectFormatter)->method('format')->willReturn($contractError);
        $this->eventDispatcher->expects($expectsDispatcher)->method('dispatch');

        try {
            $this->listener->onKernelException(new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception));
        } catch (\Throwable $e) {
            dd($e);
        }

        self::assertEmpty($request->request->all());
        self::assertSame($headers, HeaderHelper::getHeaders($request));
    }

    public function caseProvider(): iterable
    {
        yield 'with header content-type equals application/json' => [
            'headers' => ['content-type' => 'application/json'],
            'expects_formatter' => self::once(),
            'dispatcher_formatter' => self::once(),
        ];

        yield 'with header accept equals application/json' => [
            'headers' => ['accept' => 'application/json'],
            'expects_formatter' => self::once(),
            'dispatcher_formatter' => self::once(),
        ];

        yield 'with headers accept and content-type equals application/json' => [
            'headers' => ['content-type' => 'application/json', 'accept' => 'application/json'],
            'expects_formatter' => self::once(),
            'dispatcher_formatter' => self::once(),
        ];

        yield 'with headers content-type equals application/xml' => [
            'headers' => ['content-type' => 'application/xml'],
            'expects_formatter' => self::never(),
            'dispatcher_formatter' => self::never(),
        ];

        yield 'with headers accept equals application/xml' => [
            'headers' => ['accept' => 'application/xml'],
            'expects_formatter' => self::never(),
            'dispatcher_formatter' => self::never(),
        ];

        yield 'with headers accept and content-type equals application/xml' => [
            'headers' => ['content-type' => 'application/xml', 'accept' => 'application/xml'],
            'expects_formatter' => self::never(),
            'dispatcher_formatter' => self::never(),
        ];
    }
}
