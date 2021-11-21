<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\EventListener;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Converter\ConverterInterface as Converter;
use FRZB\Component\RequestMapper\Data\ConverterData;
use FRZB\Component\RequestMapper\Event\ListenerExceptionEvent;
use FRZB\Component\RequestMapper\Request\HasHeaders;
use FRZB\Component\RequestMapper\Utils\HeadersUtil;
use FRZB\Component\RequestMapper\Utils\ParamConverterUtil;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as EventDispatcher;

#[AsEventListener(event: KernelEvents::CONTROLLER, method: 'onKernelController', priority: -255)]
final class RequestMapperListener
{
    public function __construct(
        private Converter $converter,
        private EventDispatcher $dispatcher,
    ) {
    }

    /** @throws \Throwable */
    public function onKernelController(ControllerEvent $event): void
    {
        try {
            $request = $event->getRequest();
            $method = $this->getReflectionMethod($event->getController());
            $attributes = $this->getAttributes($method);
        } catch (\Throwable $e) {
            $this->dispatcher->dispatch(new ListenerExceptionEvent($event, $e, self::class));

            throw $e;
        }

        foreach ($method->getParameters() as $parameter) {
            $parameterType = (string) $parameter->getType();
            $attribute = ParamConverterUtil::getAttribute($parameter, $attributes);
            $isNativeRequest = is_a($request, $parameterType) || is_subclass_of($request, $parameterType);

            if (!$attribute || $isNativeRequest) {
                continue;
            }

            try {
                $object = $this->converter->convert(new ConverterData($request, $attribute));
            } catch (\Throwable $e) {
                $this->dispatcher->dispatch(new ListenerExceptionEvent($event, $e, self::class));

                throw $e;
            }

            if ($object instanceof HasHeaders) {
                $object->setHeaders(HeadersUtil::getHeaders($request));
            }

            $request->attributes->set($attribute->getParameterName() ?? $parameter->getName(), $object);
        }
    }

    /** @throws \ReflectionException */
    private function getReflectionMethod(mixed $controller): \ReflectionMethod|\ReflectionFunction
    {
        return match (true) {
            \is_array($controller) => new \ReflectionMethod($controller[0], $controller[1]),
            \is_object($controller) && \is_callable([$controller, '__invoke']) => new \ReflectionMethod($controller, '__invoke'),
            default => new \ReflectionFunction($controller),
        };
    }

    /** @return array<ParamConverter> */
    private function getAttributes(\ReflectionMethod|\ReflectionFunction $method): array
    {
        return ParamConverterUtil::fromReflectionAttributes(
            ...$method->getAttributes(ParamConverter::class, \ReflectionAttribute::IS_INSTANCEOF)
        );
    }
}
