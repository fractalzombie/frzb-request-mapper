<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Func\Converter;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Converter\RequestConverter;
use FRZB\Component\RequestMapper\Data\Context;
use FRZB\Component\RequestMapper\Data\ErrorInterface;
use FRZB\Component\RequestMapper\Data\ValidationError;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use FRZB\Component\RequestMapper\Tests\Helper\RequestHelper;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use FRZB\Component\RequestMapper\Tests\Stub\Enum\TestEnum;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequestWithEnum;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;

/**
 * @group request-mapper
 *
 * @internal
 */
final class RequestConverterTest extends KernelTestCase
{
    private RequestConverter $converter;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->converter = self::getContainer()->get(RequestConverter::class);
    }

    /**
     * @param ErrorInterface[] $errors
     *
     * @dataProvider caseProvider
     */
    public function testConvertMethod(Context $context, ?CreateUserRequestWithEnum $request = null, array $errors = []): void
    {
        try {
            $object = $this->converter->convert($context);
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

        self::assertSame($request::class, $object::class);
        self::assertSame($request?->getName(), $object->getName());
        self::assertSame($request?->getUserId(), $object->getUserId());
        self::assertSame($request?->getAmount(), $object->getAmount());
        self::assertSame($request?->getTestEnum(), $object->getTestEnum());
    }

    /** @throws \Exception */
    public function caseProvider(): iterable
    {
        $params = ['name' => TestConstant::USER_NAME, 'userId' => TestConstant::USER_ID, 'amount' => TestConstant::USER_AMOUNT, 'testEnum' => TestEnum::One->value];
        $attribute = new ParamConverter(parameterClass: CreateUserRequestWithEnum::class, parameterName: 'request');
        $request = RequestHelper::makeRequest(method: Request::METHOD_POST, params: $params);
        $userRequest = new CreateUserRequestWithEnum(...['name' => TestConstant::USER_NAME, 'userId' => TestConstant::USER_ID, 'amount' => TestConstant::USER_AMOUNT, 'testEnum' => TestEnum::One]);

        yield 'Converter data with valid params' => [
            'context' => new Context($request, $attribute),
            'request' => $userRequest,
        ];

        $attribute = new ParamConverter(parameterClass: CreateUserRequestWithEnum::class);
        $request = RequestHelper::makeRequest(method: Request::METHOD_POST);
        $errors = [
            new ValidationError(NotBlank::class, '[name]', 'This value should not be blank.'),
        ];

        yield 'Converter data with empty params' => [
            'context' => new Context($request, $attribute),
            'request' => null,
            'errors' => $errors,
        ];

        $params = ['name' => random_int(\PHP_INT_MIN, \PHP_INT_MAX), 'userId' => random_int(\PHP_INT_MIN, \PHP_INT_MAX), 'amount' => 'some amount', 'testEnum' => 1];
        $attribute = new ParamConverter(parameterClass: CreateUserRequestWithEnum::class);
        $request = RequestHelper::makeRequest(method: Request::METHOD_POST, params: $params);
        $errors = [
            new ValidationError(Type::class, '[name]', 'This value should be of type string.'),
            new ValidationError(Uuid::class, '[userId]', 'This is not a valid UUID.'),
            new ValidationError(Type::class, '[userId]', 'This value should be of type string.'),
            new ValidationError(Type::class, '[amount]', 'This value should be of type float.'),
            new ValidationError(Type::class, '[testEnum]', 'This value should be of type string.'),
        ];

        yield 'Converter data with invalid params' => [
            'context' => new Context($request, $attribute),
            'request' => null,
            'errors' => $errors,
        ];
    }
}
