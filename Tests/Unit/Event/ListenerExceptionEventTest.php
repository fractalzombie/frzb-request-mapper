<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Event;

use FRZB\Component\RequestMapper\Event\ListenerExceptionEvent;
use FRZB\Component\RequestMapper\EventListener\ExceptionListener;
use FRZB\Component\RequestMapper\Tests\Helper\RequestHelper;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @group request-mapper
 *
 * @internal
 */
class ListenerExceptionEventTest extends TestCase
{
    public function testConstructorMethod(): void
    {
        $event = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            RequestHelper::makeRequest(Request::METHOD_POST),
            HttpKernelInterface::MAIN_REQUEST
        );
        $exception = new \TypeError(TestConstant::EXCEPTION_MESSAGE);

        $listenerExceptionEvent = new ListenerExceptionEvent($event, $exception, ExceptionListener::class);

        self::assertSame($listenerExceptionEvent->getException()::class, \TypeError::class);
        self::assertSame($listenerExceptionEvent->getListenerClass(), ExceptionListener::class);
        self::assertSame($listenerExceptionEvent->getEvent()::class, RequestEvent::class);
        self::assertNull($listenerExceptionEvent->getErrorContract());
    }
}
