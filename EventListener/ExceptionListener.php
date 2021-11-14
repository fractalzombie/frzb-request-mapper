<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\EventListener;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Event\ListenerExceptionEvent;
use FRZB\Component\RequestMapper\ExceptionFormatter\ExceptionFormatterInterface as ExceptionFormatter;
use FRZB\Component\RequestMapper\Utils\Header;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as EventDispatcher;

#[AsEventListener(event: KernelEvents::EXCEPTION, method: 'onKernelException', priority: 10)]
final class ExceptionListener
{
    public function __construct(
        private ExceptionFormatter $exceptionFormatter,
        private EventDispatcher $eventDispatcher,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $contentType = $request->headers->get(Header::CONTENT_TYPE);
        $acceptType = $request->headers->get(Header::ACCEPT);

        if ($this->isAllowed($contentType) || $this->isAllowed($acceptType)) {
            $errorContract = $this->exceptionFormatter->format($event->getThrowable());

            $this->eventDispatcher->dispatch(
                new ListenerExceptionEvent($event, $event->getThrowable(), self::class, $errorContract)
            );
        }
    }

    #[Pure]
    public function isAllowed(?string $contentType): bool
    {
        return \in_array($contentType, ParamConverter::ALLOWED_CONTENT_TYPES, true);
    }
}
