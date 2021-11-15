<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\EventListener;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Data\ConverterData;
use FRZB\Component\RequestMapper\Event\ListenerExceptionEvent;
use FRZB\Component\RequestMapper\Locator\ConverterLocatorInterface as ConverterLocator;
use FRZB\Component\RequestMapper\Request\HasHeaders;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as EventDispatcher;

#[AsEventListener(event: KernelEvents::CONTROLLER, method: 'onKernelController', priority: -255)]
final class RequestMapperListener
{
    public function __construct(
        private ConverterLocator $converterLocator,
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
            $attribute = $attributes[$parameter->getName()] ?? null;
            $parameterType = (string) $parameter->getType();
            $isRequest = is_a($request, $parameterType) || is_subclass_of($request, $parameterType);

            if (!$attribute || $isRequest) {
                continue;
            }

            try {
                $object = $this->converterLocator->get($attribute->getType())->convert(new ConverterData($request, $attribute));
            } catch (\Throwable $e) {
                $this->dispatcher->dispatch(new ListenerExceptionEvent($event, $e, self::class));

                throw $e;
            }

            if ($object instanceof HasHeaders) {
                $object->setHeaders($request->headers->all());
            }

            $request->attributes->set($attribute->getName(), $object);
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

    /**
     * @return array<ParamConverter>
     *
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    private function getAttributes(\ReflectionMethod|\ReflectionFunction $method): array
    {
        $reflectionAttributes = $method->getAttributes(ParamConverter::class);

        if (!$reflectionAttributes) {
            return [];
        }

        $mapReflection = static fn (\ReflectionAttribute $ra): ParamConverter => $ra->newInstance();
        $mapAttributes = static fn (ParamConverter $pc): array => [$pc->getName() => $pc];

        return array_merge(...array_map($mapAttributes, array_map($mapReflection, $reflectionAttributes)));
    }
}
