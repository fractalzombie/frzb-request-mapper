<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Func\EventListener;

use Faker\Factory;
use Faker\Generator;
use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\EventListener\RequestMapperListener;
use FRZB\Component\RequestMapper\Tests\Helper\RequestHelper;
use FRZB\Component\RequestMapper\Tests\Stub\Controller\TestCallableController;
use FRZB\Component\RequestMapper\Tests\Stub\Controller\TestCallableWithoutParameterNameAndParameterClassController;
use FRZB\Component\RequestMapper\Tests\Stub\Controller\TestCallableWithoutParameterNameController;
use FRZB\Component\RequestMapper\Tests\Stub\Controller\TestController;
use FRZB\Component\RequestMapper\Tests\Stub\Controller\TestWithoutParameterNameAndParameterClassController;
use FRZB\Component\RequestMapper\Tests\Stub\Controller\TestWithoutParameterNameController;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestWithHeadersRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface as HttpKernel;

#[Group('request-mapper')]
/**
 * @internal
 */
final class RequestMapperListenerTest extends KernelTestCase
{
    private Generator $generator;
    private RequestMapperListener $listener;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->generator = Factory::create();
        $this->listener = self::getContainer()->get(RequestMapperListener::class);
    }

    #[DataProvider('dataProvider')]
    public function testOnKernelController(
        array $params,
        string $httpMethod,
        string $targetClass,
        string $parameterName,
        callable|object|array $controller
    ): void {
        $headers = ['content-type' => 'application/json'];
        $request = RequestHelper::makeRequest(method: $httpMethod, params: $params, headers: $headers, generator: $this->generator);
        $controllerEvent = $this->makeControllerEvent($request, $controller);

        $this->listener->onKernelController($controllerEvent);

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

        yield 'Test function controller with headers request' => [
            'params' => array_merge($params, ['headers' => ['content-type' => 'application/json']]),
            'http_method' => Request::METHOD_POST,
            'target_class' => TestWithHeadersRequest::class,
            'parameter_name' => 'dto',
            'controller' => #[ParamConverter(parameterClass: TestWithHeadersRequest::class, parameterName: 'dto')] static fn (TestWithHeadersRequest $dto) => null,
        ];

        yield 'Test function controller native request' => [
            'params' => $params,
            'http_method' => Request::METHOD_POST,
            'target_class' => TestRequest::class,
            'parameter_name' => 'dto',
            'controller' => #[ParamConverter(parameterClass: TestRequest::class, parameterName: 'dto')] static fn (TestRequest $dto, Request $request) => null,
        ];
    }

    private function makeControllerEvent(Request $request, mixed $controller): ControllerEvent
    {
        return new ControllerEvent(self::$kernel, $controller, $request, HttpKernel::MAIN_REQUEST);
    }
}
