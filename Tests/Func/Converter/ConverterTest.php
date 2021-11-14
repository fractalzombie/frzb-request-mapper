<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Func\Converter;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Converter\AttributeConverter;
use FRZB\Component\RequestMapper\Converter\QueryConverter;
use FRZB\Component\RequestMapper\Converter\RequestConverter;
use FRZB\Component\RequestMapper\Data\ConverterData;
use FRZB\Component\RequestMapper\Data\Error;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use FRZB\Component\RequestMapper\Locator\ConverterLocatorInterface as ConverterLocator;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Utils\RequestHelper;
use FRZB\Component\RequestMapper\Tests\Utils\TestConstant;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group request-mapper
 *
 * @internal
 */
final class ConverterTest extends KernelTestCase
{
    private ConverterLocator $locator;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->locator = self::getContainer()->get(ConverterLocator::class);
    }

    /**
     * @param Error[] $errors
     *
     * @dataProvider caseProvider
     */
    public function testConvertMethod(
        string $converterType,
        string $converterClass,
        ConverterData $data,
        ?CreateUserRequest $request = null,
        array $errors = []
    ): void {
        try {
            $converter = $this->locator->get($converterType);
            $object = $converter->convert($data);
        } catch (ValidationException $e) {
            self::assertSame(ValidationException::DEFAULT_MESSAGE, $e->getMessage());

            foreach ($errors as $index => $error) {
                $veError = $e->getErrors()[$index];

                self::assertSame($error->getMessage(), $veError->getMessage());
                self::assertSame($error->getField(), $veError->getField());
            }

            return;
        }

        self::assertSame($request::class, $object::class);
        self::assertSame($request?->getName(), $object?->getName());
        self::assertSame($request?->getUserId(), $object?->getUserId());
        self::assertSame($request?->getAmount(), $object?->getAmount());
        self::assertSame($converterType, $converter::getType());
        self::assertSame($converterClass, $converter::class);
    }

    /** @throws \Exception */
    public function caseProvider(): iterable
    {
        $params = ['name' => TestConstant::USER_NAME, 'userId' => TestConstant::USER_ID, 'amount' => TestConstant::USER_AMOUNT];
        $attribute = new ParamConverter('request', RequestConverter::getType(), CreateUserRequest::class);
        $request = RequestHelper::makeRequest(method: Request::METHOD_POST, params: $params);
        $userRequest = new CreateUserRequest(...$params);

        yield sprintf('Convert data with "%s" and valid params', RequestConverter::class) => [
            'converter_type' => RequestConverter::getType(),
            'converter_class' => RequestConverter::class,
            'converter_data' => new ConverterData($request, $attribute),
            'user_request' => $userRequest,
        ];

        $params = ['name' => TestConstant::USER_NAME, 'userId' => TestConstant::USER_ID, 'amount' => TestConstant::USER_AMOUNT];
        $attribute = new ParamConverter('request', QueryConverter::getType(), CreateUserRequest::class);
        $request = RequestHelper::makeRequest(method: Request::METHOD_GET, params: $params);
        $userRequest = new CreateUserRequest(...$params);

        yield sprintf('Convert data with "%s" and valid params', QueryConverter::class) => [
            'converter_type' => QueryConverter::getType(),
            'converter_class' => QueryConverter::class,
            'converter_data' => new ConverterData($request, $attribute),
            'user_request' => $userRequest,
        ];

        $params = ['name' => TestConstant::USER_NAME, 'userId' => TestConstant::USER_ID, 'amount' => TestConstant::USER_AMOUNT];
        $attribute = new ParamConverter('request', AttributeConverter::getType(), CreateUserRequest::class);
        $request = RequestHelper::makeRequest(method: Request::METHOD_POST, params: $params);
        $userRequest = new CreateUserRequest(...$params);

        yield sprintf('Convert data with "%s" and valid params', AttributeConverter::class) => [
            'converter_type' => AttributeConverter::getType(),
            'converter_class' => AttributeConverter::class,
            'converter_data' => new ConverterData($request, $attribute),
            'user_request' => $userRequest,
        ];

        $attribute = new ParamConverter('request', RequestConverter::getType(), CreateUserRequest::class);
        $request = RequestHelper::makeRequest(method: Request::METHOD_POST);
        $errors = [
            new Error('name', 'This value should not be blank.'),
        ];

        yield sprintf('Convert data with "%s" and empty params', RequestConverter::class) => [
            'converter_type' => RequestConverter::getType(),
            'converter_class' => RequestConverter::class,
            'converter_data' => new ConverterData($request, $attribute),
            'user_request' => null,
            'errors' => $errors,
        ];

        $attribute = new ParamConverter('request', QueryConverter::getType(), CreateUserRequest::class);
        $request = RequestHelper::makeRequest(method: Request::METHOD_GET);
        $errors = [
            new Error('name', 'This value should not be blank.'),
        ];

        yield sprintf('Convert data with "%s" and empty params', QueryConverter::class) => [
            'converter_type' => QueryConverter::getType(),
            'converter_class' => QueryConverter::class,
            'converter_data' => new ConverterData($request, $attribute),
            'user_request' => null,
            'errors' => $errors,
        ];

        $attribute = new ParamConverter('request', AttributeConverter::getType(), CreateUserRequest::class);
        $request = RequestHelper::makeRequest(method: Request::METHOD_POST);
        $errors = [
            new Error('name', 'This value should not be blank.'),
        ];

        yield sprintf('Convert data with "%s" and empty params', AttributeConverter::class) => [
            'converter_type' => AttributeConverter::getType(),
            'converter_class' => AttributeConverter::class,
            'converter_data' => new ConverterData($request, $attribute),
            'user_request' => null,
            'errors' => $errors,
        ];

        $params = ['name' => random_int(\PHP_INT_MIN, \PHP_INT_MAX), 'userId' => random_int(\PHP_INT_MIN, \PHP_INT_MAX), 'amount' => 'some amount'];
        $attribute = new ParamConverter('request', RequestConverter::getType(), CreateUserRequest::class);
        $request = RequestHelper::makeRequest(method: Request::METHOD_POST, params: $params);
        $errors = [
            new Error('name', 'This value should be of type string.'),
            new Error('userId', 'This is not a valid UUID.'),
            new Error('userId', 'This value should be of type string.'),
            new Error('amount', 'This value should be of type float.'),
        ];

        yield sprintf('Convert data with "%s" and invalid params', RequestConverter::class) => [
            'converter_type' => RequestConverter::getType(),
            'converter_class' => RequestConverter::class,
            'converter_data' => new ConverterData($request, $attribute),
            'user_request' => null,
            'errors' => $errors,
        ];

        $params = ['name' => random_int(\PHP_INT_MIN, \PHP_INT_MAX), 'userId' => random_int(\PHP_INT_MIN, \PHP_INT_MAX), 'amount' => 'some amount'];
        $attribute = new ParamConverter('request', QueryConverter::getType(), CreateUserRequest::class);
        $request = RequestHelper::makeRequest(method: Request::METHOD_GET, params: $params);
        $errors = [
            new Error('name', 'This value should be of type string.'),
            new Error('userId', 'This is not a valid UUID.'),
            new Error('userId', 'This value should be of type string.'),
            new Error('amount', 'This value should be of type float.'),
        ];

        yield sprintf('Convert data with "%s" and invalid params', QueryConverter::class) => [
            'converter_type' => QueryConverter::getType(),
            'converter_class' => QueryConverter::class,
            'converter_data' => new ConverterData($request, $attribute),
            'user_request' => null,
            'errors' => $errors,
        ];

        $params = ['name' => random_int(\PHP_INT_MIN, \PHP_INT_MAX), 'userId' => random_int(\PHP_INT_MIN, \PHP_INT_MAX), 'amount' => 'some amount'];
        $attribute = new ParamConverter('request', AttributeConverter::getType(), CreateUserRequest::class);
        $request = RequestHelper::makeRequest(method: Request::METHOD_POST, params: $params);
        $errors = [
            new Error('name', 'This value should be of type string.'),
            new Error('userId', 'This is not a valid UUID.'),
            new Error('userId', 'This value should be of type string.'),
            new Error('amount', 'This value should be of type float.'),
        ];

        yield sprintf('Convert data with "%s" and invalid params', AttributeConverter::class) => [
            'converter_type' => AttributeConverter::getType(),
            'converter_class' => AttributeConverter::class,
            'converter_data' => new ConverterData($request, $attribute),
            'user_request' => null,
            'errors' => $errors,
        ];
    }
}
