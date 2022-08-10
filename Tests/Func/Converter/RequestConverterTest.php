<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Func\Converter;

use FRZB\Component\RequestMapper\Attribute\RequestBody;
use FRZB\Component\RequestMapper\Converter\RequestConverter;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use FRZB\Component\RequestMapper\Tests\Helper\RequestHelper;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use FRZB\Component\RequestMapper\Tests\Stub\Enum\TestEnum;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequestWithEnumAndNestedObjectAndArrayOfObjects;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserSettingsRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\NestedObject;
use FRZB\Component\RequestMapper\ValueObject\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;

/** @internal */
#[Group('request-mappera')]
final class RequestConverterTest extends KernelTestCase
{
    private RequestConverter $converter;

    /** @throws \Throwable */
    protected function setUp(): void
    {
        self::bootKernel();

        $this->converter = self::getContainer()->get(RequestConverter::class);
    }

    #[DataProvider('caseProvider')]
    public function testConvertMethod(Request $mainRequest, RequestBody $requestBody, ?CreateUserRequestWithEnumAndNestedObjectAndArrayOfObjects $typedRequest = null, array $errors = []): void
    {
        try {
            $object = $this->converter->convert($mainRequest, $requestBody);
        } catch (ValidationException $e) {
            self::assertSame(ValidationException::DEFAULT_MESSAGE, $e->getMessage());

            foreach ($errors as $index => $error) {
                $veError = $e->getErrors()[$index];

                self::assertSame($error->getMessage(), $veError->getMessage());
                self::assertSame($error->getField(), $veError->getField());
                self::assertSame($error->getType(), $veError->getType());
            }

            return;
        }

        self::assertSame($typedRequest::class, $object::class);
        self::assertSame($typedRequest?->name, $object->name);
        self::assertSame($typedRequest?->userId, $object->userId);
        self::assertSame($typedRequest?->amount, $object->amount);
        self::assertSame($typedRequest?->testEnum, $object->testEnum);
        self::assertSame($typedRequest?->nested->type, $object->nested->type);
        self::assertSame($typedRequest->nestedObjects[0]->type, $object->nestedObjects[0]->type);
        self::assertSame($typedRequest->nestedObjects[1]->type, $object->nestedObjects[1]->type);
    }

    /** @throws \Exception */
    public function caseProvider(): iterable
    {
        $nestedObjectParams = ['type' => TestConstant::USER_NAME];
        $nestedObject = new NestedObject(...$nestedObjectParams);
        $nestedObjects = [new NestedObject(...$nestedObjectParams), new NestedObject(...$nestedObjectParams)];
        $params = ['name' => TestConstant::USER_NAME, 'nested' => $nestedObjectParams, 'userId' => TestConstant::USER_ID, 'amount' => TestConstant::USER_AMOUNT, 'testEnum' => TestEnum::One->value, 'nestedObjects' => [$nestedObjectParams, $nestedObjectParams]];
        $attribute = new RequestBody(requestClass: CreateUserRequestWithEnumAndNestedObjectAndArrayOfObjects::class, argumentName: 'request');
        $request = RequestHelper::makeRequest(method: Request::METHOD_POST, params: $params);
        $userRequest = new CreateUserRequestWithEnumAndNestedObjectAndArrayOfObjects(...['name' => TestConstant::USER_NAME, 'nested' => $nestedObject, 'userId' => TestConstant::USER_ID, 'amount' => TestConstant::USER_AMOUNT, 'testEnum' => TestEnum::One, 'nestedObjects' => $nestedObjects]);

        yield 'Converter data with valid params' => [
            'mainRequest' => $request,
            'requestBody' => $attribute,
            'request' => $userRequest,
        ];

        $attribute = new RequestBody(requestClass: CreateUserRequestWithEnumAndNestedObjectAndArrayOfObjects::class);
        $request = RequestHelper::makeRequest(method: Request::METHOD_POST);
        $errors = [
            new ValidationError(NotBlank::class, '[name]', 'This value should not be blank.'),
        ];

        yield 'Converter data with empty params' => [
            'mainRequest' => $request,
            'requestBody' => $attribute,
            'request' => null,
            'errors' => $errors,
        ];

        $params = ['name' => random_int(\PHP_INT_MIN, \PHP_INT_MAX), 'userId' => random_int(\PHP_INT_MIN, \PHP_INT_MAX), 'amount' => 'some amount', 'testEnum' => 1];
        $attribute = new RequestBody(requestClass: CreateUserRequestWithEnumAndNestedObjectAndArrayOfObjects::class);
        $request = RequestHelper::makeRequest(method: Request::METHOD_POST, params: $params);
        $errors = [
            new ValidationError(Type::class, '[name]', 'This value should be of type string.'),
            new ValidationError(Uuid::class, '[userId]', 'This is not a valid UUID.'),
            new ValidationError(Type::class, '[userId]', 'This value should be of type string.'),
            new ValidationError(Type::class, '[amount]', 'This value should be of type float.'),
            new ValidationError(Type::class, '[testEnum]', 'This value should be of type string.'),
        ];

        yield 'Converter data with invalid params' => [
            'mainRequest' => $request,
            'requestBody' => $attribute,
            'request' => null,
            'errors' => $errors,
        ];
    }
}
