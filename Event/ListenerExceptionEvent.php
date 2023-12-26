<?php

declare(strict_types=1);

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *
 * Copyright (c) 2023 Mykhailo Shtanko fractalzombie@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE.MD
 * file that was distributed with this source code.
 */

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
    ) {}

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
