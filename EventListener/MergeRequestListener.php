<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, method: 'onKernelRequest', priority: 10)]
final class MergeRequestListener
{
    private const ROUTE_PARAMS_KEY = '_route_params';

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $payload = [
            ...$request->request->all(),
            ...$request->query->all(),
            ...$request->attributes->get(self::ROUTE_PARAMS_KEY, []),
        ];

        $request->request = new InputBag($payload);
    }
}
