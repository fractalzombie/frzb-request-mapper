<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Func\EventListener;

use Faker\Factory;
use Faker\Generator;
use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Converter\AttributeConverter;
use FRZB\Component\RequestMapper\Converter\QueryConverter;
use FRZB\Component\RequestMapper\Converter\RequestConverter;
use FRZB\Component\RequestMapper\Data\ConverterType;
use FRZB\Component\RequestMapper\EventListener\RequestMapperListener;
use FRZB\Component\RequestMapper\Tests\Stub\TestController;
use FRZB\Component\RequestMapper\Tests\Stub\TestController2;
use FRZB\Component\RequestMapper\Tests\Stub\TestController3;
use FRZB\Component\RequestMapper\Tests\Stub\TestController4;
use FRZB\Component\RequestMapper\Tests\Stub\TestController5;
use FRZB\Component\RequestMapper\Tests\Stub\TestController6;
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
     * @param array|callable|object $controller
     *
     * @dataProvider dataProvider
     *
     * @throws \Throwable
     */
    public function testOnKernelController(array $params, string $method, string $class, string $parameter, mixed $controller): void
    {
        $request = RequestHelper::makeRequest(method: $method, params: $params, generator: $this->generator);
        $controllerEvent = $this->makeControllerEvent($request, $controller);

        $this->listener->onKernelController($controllerEvent);

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

        yield sprintf('Test with callable controller with "%s"', RequestConverter::class) => [
            'params' => $params,
            'method' => Request::METHOD_POST,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => new TestController(),
        ];

        yield sprintf('Test with method controller with "%s"', RequestConverter::class) => [
            'params' => $params,
            'method' => Request::METHOD_POST,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => [new TestController2(), 'method'],
        ];

        yield sprintf('Test with function controller with "%s"', RequestConverter::class) => [
            'params' => $params,
            'method' => Request::METHOD_POST,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => #[ParamConverter('dto', ConverterType::REQUEST, TestRequest::class)] static fn (TestRequest $dto) => null,
        ];

        yield sprintf('Test with callable controller with "%s"', QueryConverter::class) => [
            'params' => $params,
            'method' => Request::METHOD_GET,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => new TestController6(),
        ];

        yield sprintf('Test with method controller with "%s"', QueryConverter::class) => [
            'params' => $params,
            'method' => Request::METHOD_GET,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => [new TestController5(), 'method'],
        ];

        yield sprintf('Test with function controller with "%s"', QueryConverter::class) => [
            'params' => $params,
            'method' => Request::METHOD_GET,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => #[ParamConverter('dto', ConverterType::QUERY, TestRequest::class)] static fn (TestRequest $dto) => null,
        ];

        yield sprintf('Test with callable controller with "%s"', AttributeConverter::class) => [
            'params' => $params,
            'method' => Request::METHOD_POST,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => new TestController3(),
        ];

        yield sprintf('Test with method controller with "%s"', AttributeConverter::class) => [
            'params' => $params,
            'method' => Request::METHOD_POST,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => [new TestController4(), 'method'],
        ];

        yield sprintf('Test with function controller with "%s"', AttributeConverter::class) => [
            'params' => $params,
            'method' => Request::METHOD_POST,
            'class' => TestRequest::class,
            'parameter' => 'dto',
            'controller' => #[ParamConverter('dto', ConverterType::ATTRIBUTE, TestRequest::class)] static fn (TestRequest $dto) => null,
        ];
    }

    private function makeControllerEvent(Request $request, mixed $controller): ControllerEvent
    {
        return new ControllerEvent(self::$kernel, $controller, $request, HttpKernel::MAIN_REQUEST);
    }
}
