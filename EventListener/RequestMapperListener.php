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

use FRZB\Component\RequestMapper\Attribute\RequestBody;
use FRZB\Component\RequestMapper\Data\HasHeaders;
use FRZB\Component\RequestMapper\Helper\HeaderHelper;
use FRZB\Component\RequestMapper\Helper\RequestBodyHelper;
use FRZB\Component\RequestMapper\RequestMapper\RequestMapperInterface as RequestMapper;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::CONTROLLER, method: 'onKernelController', priority: -255)]
class RequestMapperListener
{
    public function __construct(
        private readonly RequestMapper $mapper,
    ) {}

    /** @throws \Throwable */
    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $method = $this->getReflectionMethod($event->getController());
        $attributes = $this->getAttributes($method);

        foreach ($method->getParameters() as $parameter) {
            $parameterType = (string) $parameter->getType();
            $attribute = RequestBodyHelper::getAttribute($parameter, $attributes);
            $isNativeRequest = $request instanceof $parameterType;

            if (!$attribute || $isNativeRequest) {
                continue;
            }

            $object = $this->mapper->map($request, $attribute);

            if ($object instanceof HasHeaders) {
                $object->setHeaders(HeaderHelper::getHeaders($request));
            }

            $request->attributes->set($attribute->argumentName ?? $parameter->getName(), $object);
        }
    }

    /** @throws \ReflectionException */
    private function getReflectionMethod(array|callable|object $controller): \ReflectionFunction|\ReflectionMethod
    {
        return match (true) {
            \is_array($controller) => new \ReflectionMethod(...$controller),
            \is_object($controller) && \is_callable($controller) => new \ReflectionMethod($controller, '__invoke'),
            default => new \ReflectionFunction($controller),
        };
    }

    /** @return array<RequestBody> */
    private function getAttributes(\ReflectionFunction|\ReflectionMethod $method): array
    {
        return RequestBodyHelper::fromReflectionAttributes(
            ...$method->getAttributes(RequestBody::class, \ReflectionAttribute::IS_INSTANCEOF)
        );
    }
}
