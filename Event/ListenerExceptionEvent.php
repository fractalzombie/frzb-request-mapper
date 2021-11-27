<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Event;

use FRZB\Component\RequestMapper\Data\ErrorContract;
use Symfony\Contracts\EventDispatcher\Event;

final class ListenerExceptionEvent extends Event
{
    public function __construct(
        private Event $event,
        private \Throwable $exception,
        private string $listenerClass,
        private ?ErrorContract $errorContract = null,
    ) {
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }

    public function getListenerClass(): string
    {
        return $this->listenerClass;
    }

    public function getErrorContract(): ?ErrorContract
    {
        return $this->errorContract;
    }
}
