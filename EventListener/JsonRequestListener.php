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

namespace FRZB\Component\RequestMapper\EventListener;

use FRZB\Component\RequestMapper\Event\ListenerExceptionEvent;
use FRZB\Component\RequestMapper\Helper\Header;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as EventDispatcher;

#[AsEventListener(event: KernelEvents::REQUEST, method: 'onKernelRequest', priority: 20)]
class JsonRequestListener
{
    private const ALLOWED_CONTENT_TYPES = ['application/json'];

    private const ALLOWED_HTTP_METHODS = [
        Request::METHOD_GET,
        Request::METHOD_PUT,
        Request::METHOD_POST,
        Request::METHOD_PATCH,
        Request::METHOD_DELETE,
    ];

    private const EXCEPTION_HEADERS = [
        Header::CONTENT_TYPE => 'application/json',
        Header::ACCEPT => 'application/json',
    ];

    public function __construct(
        private readonly EventDispatcher $dispatcher,
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $contentType = $request->headers->get(Header::CONTENT_TYPE);
        $httpMethod = $request->getMethod();

        if ($this->isHttpMethodAllowed($httpMethod) && $this->isContentTypeAllowed($contentType)) {
            try {
                $payload = json_decode((string) $request->getContent(), true, 512, \JSON_THROW_ON_ERROR);
                $request->request = new InputBag($payload ?? []);
            } catch (\JsonException $e) {
                $this->dispatcher->dispatch(new ListenerExceptionEvent($event, $e, self::class));

                throw new BadRequestHttpException($e->getMessage(), $e, (int) $e->getCode(), self::EXCEPTION_HEADERS);
            }
        }
    }

    #[Pure]
    private function isHttpMethodAllowed(string $httpMethod): bool
    {
        return \in_array($httpMethod, self::ALLOWED_HTTP_METHODS, true);
    }

    #[Pure]
    private function isContentTypeAllowed(?string $contentType): bool
    {
        return \in_array($contentType, self::ALLOWED_CONTENT_TYPES, true);
    }
}
