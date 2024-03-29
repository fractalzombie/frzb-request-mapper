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

namespace FRZB\Component\RequestMapper\Tests\Func\EventListener;

use FRZB\Component\RequestMapper\Attribute\RequestBody;
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
    private RequestMapperListener $listener;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->listener = self::getContainer()->get(RequestMapperListener::class);
    }

    #[DataProvider('dataProvider')]
    public function testOnKernelController(
        array $params,
        string $httpMethod,
        string $targetClass,
        string $parameterName,
        array|callable|object $controller
    ): void {
        $headers = ['content-type' => 'application/json'];
        $request = RequestHelper::makeRequest(method: $httpMethod, params: $params, headers: $headers);
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

    public static function dataProvider(): iterable
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
            'controller' => #[RequestBody(requestClass: TestRequest::class, argumentName: 'dto')] static fn (TestRequest $dto) => null,
        ];

        yield 'Test function controller without parameter name' => [
            'params' => $params,
            'http_method' => Request::METHOD_POST,
            'target_class' => TestRequest::class,
            'parameter_name' => 'dto',
            'controller' => #[RequestBody(requestClass: TestRequest::class)] static fn (TestRequest $dto) => null,
        ];

        yield 'Test function controller without parameter name and parameter class' => [
            'params' => $params,
            'http_method' => Request::METHOD_POST,
            'target_class' => TestRequest::class,
            'parameter_name' => 'dto',
            'controller' => #[RequestBody] static fn (TestRequest $dto) => null,
        ];

        yield 'Test function controller with headers request' => [
            'params' => array_merge($params, ['headers' => ['content-type' => 'application/json']]),
            'http_method' => Request::METHOD_POST,
            'target_class' => TestWithHeadersRequest::class,
            'parameter_name' => 'dto',
            'controller' => #[RequestBody(requestClass: TestWithHeadersRequest::class, argumentName: 'dto')] static fn (TestWithHeadersRequest $dto) => null,
        ];

        yield 'Test function controller native request' => [
            'params' => $params,
            'http_method' => Request::METHOD_POST,
            'target_class' => TestRequest::class,
            'parameter_name' => 'dto',
            'controller' => #[RequestBody(requestClass: TestRequest::class, argumentName: 'dto')] static fn (TestRequest $dto, Request $request) => null,
        ];
    }

    private function makeControllerEvent(Request $request, mixed $controller): ControllerEvent
    {
        return new ControllerEvent(self::$kernel, $controller, $request, HttpKernel::MAIN_REQUEST);
    }
}
