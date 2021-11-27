<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\EventListener;

use FRZB\Component\RequestMapper\Event\ListenerExceptionEvent;
use FRZB\Component\RequestMapper\ExceptionFormatter\ExceptionFormatterInterface as ExceptionFormatter;
use FRZB\Component\RequestMapper\Helper\Header;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as EventDispatcher;

#[AsEventListener(event: KernelEvents::EXCEPTION, method: 'onKernelException', priority: 20)]
final class ExceptionListener
{
    private const ALLOWED_CONTENT_TYPE = 'application/json';

    public function __construct(
        private ExceptionFormatter $exceptionFormatter,
        private EventDispatcher $eventDispatcher,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $isContentTypeAllowed = $request->headers->contains(Header::CONTENT_TYPE, self::ALLOWED_CONTENT_TYPE);
        $isAcceptTypeAllowed = $request->headers->contains(Header::ACCEPT, self::ALLOWED_CONTENT_TYPE);

        if ($isContentTypeAllowed || $isAcceptTypeAllowed) {
            $errorContract = $this->exceptionFormatter->format($event->getThrowable());

            $this->eventDispatcher->dispatch(new ListenerExceptionEvent($event, $event->getThrowable(), self::class, $errorContract));
        }
    }
}
