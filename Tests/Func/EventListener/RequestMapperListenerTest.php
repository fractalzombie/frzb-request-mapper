<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Func\EventListener;

use Faker\Factory;
use Faker\Generator;
use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\EventListener\RequestMapperListener;
use FRZB\Component\RequestMapper\Tests\Stub\TestCallableController;
use FRZB\Component\RequestMapper\Tests\Stub\TestCallableControllerWithoutParameterName;
use FRZB\Component\RequestMapper\Tests\Stub\TestController;
use FRZB\Component\RequestMapper\Tests\Stub\TestControllerWithoutParameterName;
use FRZB\Component\RequestMapper\Tests\Stub\TestRequest;
use FRZB\Component\RequestMapper\Tests\Utils\RequestHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface as HttpKernel;

/**
 * @group request-mapper
 *
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

    /**
     * @dataProvider dataProvider
     *
     * @throws \Throwable
     */
    public function testOnKernelController(array $params, string $method, string $class, string $parameter, callable|object|array $controller): void
    {
        $request = RequestHelper::makeRequest(method: $method, params: $params, generator: $this->generator);
        $controllerEvent = $this->makeControllerEvent($request, $controller);

        $this->listener->onKernelController($controllerEvent);

        self::assertNotNull($request->attributes->get($parameter));
        self::assertSame($class, $request->attributes->get($parameter)::class);

        foreach ($params as $param => $value) {
            $object = $request->attributes->get($parameter);
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
            'method' => Request::METHOD_POST,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => new TestCallableController(),
        ];

        yield 'Test callable controller without parameter name' => [
            'params' => $params,
            'method' => Request::METHOD_POST,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => new TestCallableControllerWithoutParameterName(),
        ];

        yield 'Test controller' => [
            'params' => $params,
            'method' => Request::METHOD_POST,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => [new TestController(), 'method'],
        ];

        yield 'Test controller without parameter name' => [
            'params' => $params,
            'method' => Request::METHOD_POST,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => [new TestControllerWithoutParameterName(), 'method'],
        ];

        yield 'Test function controller' => [
            'params' => $params,
            'method' => Request::METHOD_POST,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => #[ParamConverter(class: TestRequest::class, name: 'dto')] static fn (TestRequest $dto) => null,
        ];

        yield 'Test function controller without parameter name' => [
            'params' => $params,
            'method' => Request::METHOD_POST,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => #[ParamConverter(class: TestRequest::class)] static fn (TestRequest $dto) => null,
        ];
    }

    private function makeControllerEvent(Request $request, mixed $controller): ControllerEvent
    {
        return new ControllerEvent(self::$kernel, $controller, $request, HttpKernel::MAIN_REQUEST);
    }
}
