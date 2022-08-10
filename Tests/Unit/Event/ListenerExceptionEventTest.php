<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Event;

use FRZB\Component\RequestMapper\Event\ListenerExceptionEvent;
use FRZB\Component\RequestMapper\EventListener\ExceptionListener;
use FRZB\Component\RequestMapper\Tests\Helper\RequestHelper;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/** @internal */
#[Group('request-mapper')]
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

        self::assertSame(\TypeError::class, $listenerExceptionEvent->getException()::class);
        self::assertSame(ExceptionListener::class, $listenerExceptionEvent->getListenerClass());
        self::assertSame(RequestEvent::class, $listenerExceptionEvent->getEvent()::class);
        self::assertNull($listenerExceptionEvent->getErrorContract());
    }
}
