<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\EventListener;

use Faker\Factory;
use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Converter\ConverterInterface as Converter;
use FRZB\Component\RequestMapper\EventListener\RequestMapperListener;
use FRZB\Component\RequestMapper\Tests\Helper\RequestHelper;
use FRZB\Component\RequestMapper\Tests\Stub\Controller\TestCallableController;
use FRZB\Component\RequestMapper\Tests\Stub\Controller\TestCallableWithoutParameterNameAndParameterClassController;
use FRZB\Component\RequestMapper\Tests\Stub\Controller\TestCallableWithoutParameterNameController;
use FRZB\Component\RequestMapper\Tests\Stub\Controller\TestController;
use FRZB\Component\RequestMapper\Tests\Stub\Controller\TestWithoutParameterNameAndParameterClassController;
use FRZB\Component\RequestMapper\Tests\Stub\Controller\TestWithoutParameterNameController;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface as HttpKernel;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * @group request-mapper
 *
 * @internal
 */
final class RequestMapperListenerTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @throws \Throwable
     */
    public function testOnKernelController(
        array $params,
        string $httpMethod,
        string $targetClass,
        string $parameterName,
        callable|object|array $controller
    ): void {
        $request = RequestHelper::makeRequest(method: $httpMethod, params: $params, generator: Factory::create());
        $converter = $this->makeConverter($targetClass, $params);
        $eventDispatcher = $this->makeEventDispatcherMock();
        $controllerEvent = $this->makeControllerEvent($request, $controller);

        (new RequestMapperListener($converter, $eventDispatcher))->onKernelController($controllerEvent);

        self::assertNotNull($request->attributes->get($parameterName));
        self::assertSame($targetClass, $request->attributes->get($parameterName)::class);

        foreach ($params as $param => $value) {
            $object = $request->attributes->get($parameterName);
            $paramRef = new \ReflectionProperty($object, $param);
            $paramRef->setAccessible(true);
            self::assertSame($value, $paramRef->getValue($object));
        }
    }

    public function dataProvider(): iterable
    {
        $params = ['name' => 'some name', 'model' => 'Product'];

        yield 'Test callable controller' => [
            'params' => $params,
            'http_method' => Request::METHOD_POST,
            'target_class' => TestRequest::class,
            'parameter_name' => 'dto',
            'controller' => new TestCallableController(),
        ];

        yield 'Test callable controller without parameter name' => [
            'params' => $params,
            'http_method' => Request::METHOD_POST,
            'target_class' => TestRequest::class,
            'parameter_name' => 'dto',
            'controller' => new TestCallableWithoutParameterNameController(),
        ];

        yield 'Test callable controller without parameter name and parameter class' => [
            'params' => $params,
            'http_method' => Request::METHOD_POST,
            'target_class' => TestRequest::class,
            'parameter_name' => 'dto',
            'controller' => new TestCallableWithoutParameterNameAndParameterClassController(),
        ];

        yield 'Test controller' => [
            'params' => $params,
            'http_method' => Request::METHOD_POST,
            'target_class' => TestRequest::class,
            'parameter_name' => 'dto',
            'controller' => [new TestController(), 'method'],
        ];

        yield 'Test controller without parameter name' => [
            'params' => $params,
            'http_method' => Request::METHOD_POST,
            'target_class' => TestRequest::class,
            'parameter_name' => 'dto',
            'controller' => [new TestWithoutParameterNameController(), 'method'],
        ];

        yield 'Test controller without parameter name and parameter class' => [
            'params' => $params,
            'http_method' => Request::METHOD_POST,
            'target_class' => TestRequest::class,
            'parameter_name' => 'dto',
            'controller' => [new TestWithoutParameterNameAndParameterClassController(), 'method'],
        ];

        yield 'Test function controller' => [
            'params' => $params,
            'http_method' => Request::METHOD_POST,
            'target_class' => TestRequest::class,
            'parameter_name' => 'dto',
            'controller' => #[ParamConverter(parameterClass: TestRequest::class, parameterName: 'dto')] static fn (TestRequest $dto) => null,
        ];

        yield 'Test function controller without parameter name' => [
            'params' => $params,
            'http_method' => Request::METHOD_POST,
            'target_class' => TestRequest::class,
            'parameter_name' => 'dto',
            'controller' => #[ParamConverter(parameterClass: TestRequest::class)] static fn (TestRequest $dto) => null,
        ];

        yield 'Test function controller without parameter name and parameter class' => [
            'params' => $params,
            'http_method' => Request::METHOD_POST,
            'target_class' => TestRequest::class,
            'parameter_name' => 'dto',
            'controller' => #[ParamConverter] static fn (TestRequest $dto) => null,
        ];
    }

    private function makeConverter(string $class, array $params): Converter
    {
        $converter = $this->createMock(Converter::class);

        $converter->method('convert')->willReturn(new $class(...$params));

        return $converter;
    }

    private function makeEventDispatcherMock(): EventDispatcher
    {
        return $this->createMock(EventDispatcher::class);
    }

    private function makeControllerEvent(Request $request, mixed $controller): ControllerEvent
    {
        $kernel = $this->createMock(HttpKernel::class);

        return new ControllerEvent($kernel, $controller, $request, HttpKernel::SUB_REQUEST);
    }
}
