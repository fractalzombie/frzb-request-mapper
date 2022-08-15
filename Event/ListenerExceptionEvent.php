<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Event;

use FRZB\Component\RequestMapper\Data\ErrorContract;
use Symfony\Contracts\EventDispatcher\Event;

class ListenerExceptionEvent extends Event
{
    public function __construct(
        private readonly Event $event,
        private readonly \Throwable $exception,
        private readonly string $listenerClass,
        private readonly ?ErrorContract $errorContract = null,
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
