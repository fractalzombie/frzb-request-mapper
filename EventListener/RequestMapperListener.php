<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\EventListener;

use FRZB\Component\RequestMapper\Attribute\RequestBody;
use FRZB\Component\RequestMapper\Data\HasHeaders;
use FRZB\Component\RequestMapper\Helper\HeaderHelper;
use FRZB\Component\RequestMapper\Helper\ParamConverterHelper;
use FRZB\Component\RequestMapper\RequestMapper\RequestMapperInterface as Converter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::CONTROLLER, method: 'onKernelController', priority: -255)]
final class RequestMapperListener
{
    public function __construct(
        private readonly Converter $converter,
    ) {
    }

    /** @throws \Throwable */
    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $method = $this->getReflectionMethod($event->getController());
        $attributes = $this->getAttributes($method);

        foreach ($method->getParameters() as $parameter) {
            $parameterType = (string) $parameter->getType();
            $attribute = ParamConverterHelper::getAttribute($parameter, $attributes);
            $isNativeRequest = is_a($request, $parameterType) || is_subclass_of($request, $parameterType);

            if (!$attribute || $isNativeRequest) {
                continue;
            }

            $object = $this->converter->convert($request, $attribute);

            if ($object instanceof HasHeaders) {
                $object->setHeaders(HeaderHelper::getHeaders($request));
            }

            $request->attributes->set($attribute->argumentName ?? $parameter->getName(), $object);
        }
    }

    /** @throws \ReflectionException */
    private function getReflectionMethod(mixed $controller): \ReflectionMethod|\ReflectionFunction
    {
        return match (true) {
            \is_array($controller) => new \ReflectionMethod(/** @scrutinizer ignore-type */ ...$controller),
            \is_object($controller) && \is_callable($controller) => new \ReflectionMethod($controller, '__invoke'),
            default => new \ReflectionFunction($controller),
        };
    }

    /** @return array<RequestBody> */
    private function getAttributes(\ReflectionMethod|\ReflectionFunction $method): array
    {
        return ParamConverterHelper::fromReflectionAttributes(
            ...$method->getAttributes(RequestBody::class, \ReflectionAttribute::IS_INSTANCEOF)
        );
    }
}
