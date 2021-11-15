<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Converter;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Converter\RequestConverter;
use FRZB\Component\RequestMapper\Converter\TypeConverter;
use FRZB\Component\RequestMapper\Data\ConverterData;
use FRZB\Component\RequestMapper\Data\Error;
use FRZB\Component\RequestMapper\Exception\TypeConverterException;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use FRZB\Component\RequestMapper\Parser\ExceptionConverterInterface;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @group request-mapper-converter-1
 *
 * @internal
 */
final class RequestConverterTest extends TestCase
{
    private ValidatorInterface $validator;
    private DenormalizerInterface $denormalizer;
    private ExceptionConverterInterface $exceptionConverter;
    private TypeConverter $typeConverter;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->denormalizer = $this->createMock(DenormalizerInterface::class);
        $this->exceptionConverter = $this->createMock(ExceptionConverterInterface::class);
        $this->typeConverter = new RequestConverter($this->validator, $this->denormalizer, $this->exceptionConverter);
    }

    /**
     * @dataProvider getParams
     *
     * @throws \ReflectionException
     * @throws \FRZB\Component\RequestMapper\Exception\ValidationException
     */
    public function testItCanConvertRequestToClass(
        ConverterData $data,
        object $expected,
        ConstraintViolationList $violations,
        bool $denormalizerThrows = false,
        ?\Throwable $denormalizerException = null,
        ?string $denormalizerExceptionMessage = null
    ): void {
        self::markTestSkipped('skipped');

        if ($violations->count() > 0) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage(ValidationException::DEFAULT_MESSAGE);
        }

        if ($denormalizerThrows && $denormalizerException instanceof TypeConverterException) {
            $this->expectException(\get_class($denormalizerException));
            $this->expectExceptionMessage($denormalizerExceptionMessage);
        }

        if ($denormalizerThrows && $denormalizerException instanceof \TypeError) {
            $this->exceptionConverter
                ->expects(self::once())
                ->method('convert')
                ->willReturn(new Error('SomeType', 'someField', 'someError'))
            ;

            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage(ValidationException::DEFAULT_MESSAGE);
        }

        $denormalizeMethod = $this->denormalizer
            ->expects(self::once())
            ->method('denormalize')
        ;

        $denormalizerThrows
            ? $denormalizeMethod->willThrowException($denormalizerException)
            : $denormalizeMethod->willReturn($expected);

        if (!$denormalizerThrows) {
            $this->validator
                ->expects(self::once())
                ->method('validate')
                ->willReturn($violations)
            ;
        }

        $object = $this->typeConverter->convert($data);

        self::assertNotNull($object);
        self::assertSame($expected, $object);
    }

    public function getParams(): iterable
    {
        $payload = ['name' => 'TestName', 'userId' => 100, 'amount' => 500.10];
        $parameters = ['name' => 'request', 'method' => Request::METHOD_POST, 'class' => CreateUserRequest::class];
        $request = Request::createFromGlobals();
        $request->request = new ParameterBag($payload);
        $annotation = new ParamConverter($parameters);
        $violation = $this->makeConstraintViolation($payload);

        yield 'it can pass object to deserialize and validation when everything is ok' => $this->makeAttributes(
            $request,
            $annotation,
            $payload
        );

        yield 'it can get validation error when validator is throws' => $this->makeAttributes(
            $request,
            $annotation,
            $payload,
            $violation
        );

        yield 'it can get request mapper exception when denormalizer throws' => $this->makeAttributes(
            $request,
            $annotation,
            $payload,
            null,
            true,
            new TypeConverterException('Some logic is broken'),
            'Some logic is broken'
        );

        yield 'it can get type exception when denormalizer throws' => $this->makeAttributes(
            $request,
            $annotation,
            $payload,
            $violation,
            true,
            new \TypeError('Some logic is broken'),
            'Some logic is broken'
        );
    }

    private function makeConstraintViolation(array $payload): ConstraintViolation
    {
        return new ConstraintViolation(
            'someMessage',
            'someMessageTemplate',
            ['userId'],
            $payload,
            'userId',
            null
        );
    }

    private function makeAttributes(
        Request $request,
        ParamConverter $annotation,
        array $payload,
        ?ConstraintViolation $violation = null,
        ?bool $denormalizerThrows = false,
        ?\Throwable $denormalizerException = null,
        ?string $denormalizerExceptionMessage = null
    ): array {
        return [
            'data' => new ConverterData($request, $annotation),
            'expected' => new CreateUserRequest($payload['name'], $payload['userId'], $payload['amount']),
            'violations' => new ConstraintViolationList($violation ? [$violation] : []),
            'denormalizerThrows' => $denormalizerThrows,
            'denormalizerException' => $denormalizerException,
            'denormalizerExceptionMessage' => $denormalizerExceptionMessage,
        ];
    }
}
