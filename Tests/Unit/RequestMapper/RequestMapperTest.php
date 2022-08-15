<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\RequestMapper;

use FRZB\Component\RequestMapper\Attribute\RequestBody;
use FRZB\Component\RequestMapper\Exception\ClassExtractorException;
use FRZB\Component\RequestMapper\Exception\ConstraintException;
use FRZB\Component\RequestMapper\Exception\ConverterException;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use FRZB\Component\RequestMapper\ExceptionMapper\ExceptionMapperLocator as ExceptionMapperLocatorImpl;
use FRZB\Component\RequestMapper\ExceptionMapper\ExceptionMapperLocatorInterface as ExceptionMapperLocator;
use FRZB\Component\RequestMapper\ExceptionMapper\Mapper\MissingConstructorArgumentsExceptionMapper;
use FRZB\Component\RequestMapper\ExceptionMapper\Mapper\TypeErrorExceptionMapper;
use FRZB\Component\RequestMapper\Extractor\ConstraintExtractor;
use FRZB\Component\RequestMapper\Extractor\DiscriminatorMapExtractor;
use FRZB\Component\RequestMapper\Extractor\ParametersExtractor;
use FRZB\Component\RequestMapper\RequestMapper\RequestMapper;
use FRZB\Component\RequestMapper\Tests\Helper\RequestHelper;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateSettingsRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface as Denormalizer;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface as Validator;

#[Group('request-mapper')]
class RequestMapperTest extends TestCase
{
    private RequestMapper $converter;

    private Validator $validator;
    private Denormalizer $denormalizer;
    private ExceptionMapperLocator $exceptionMapperLocator;
    private DiscriminatorMapExtractor $classExtractor;
    private ConstraintExtractor $constraintExtractor;
    private ParametersExtractor $parametersExtractor;

    protected function setUp(): void
    {
        $exceptionMappers = [
            MissingConstructorArgumentsExceptionMapper::getType() => new MissingConstructorArgumentsExceptionMapper(),
            TypeErrorExceptionMapper::getType() => new TypeErrorExceptionMapper(),
        ];

        $services = [
            $this->validator = $this->createMock(Validator::class),
            $this->denormalizer = $this->createMock(Denormalizer::class),
            $this->exceptionMapperLocator = new ExceptionMapperLocatorImpl($exceptionMappers),
            $this->classExtractor = $this->createMock(DiscriminatorMapExtractor::class),
            $this->constraintExtractor = $this->createMock(ConstraintExtractor::class),
            $this->parametersExtractor = $this->createMock(ParametersExtractor::class),
        ];

        $this->converter = new RequestMapper(...$services);
    }

    #[DataProvider('caseProvider')]
    public function testConvertMethod(string $service, InvokedCountMatcher $expects, string $method, \Throwable $exception, string $expectedExceptionClass, array $parameters, bool $willServiceThrow = true): void
    {
        $attribute = new RequestBody(...$parameters);
        $request = RequestHelper::makeRequest(method: Request::METHOD_POST, params: []);
        $this->denormalizer->method('denormalize')->willReturn(new \stdClass());

        if ($willServiceThrow) {
            $this->{$service}->expects($expects)->method($method)->willThrowException($exception);
        }

        $this->expectException($expectedExceptionClass);

        $this->converter->map($request, $attribute);
    }

    public function caseProvider(): iterable
    {
        yield sprintf('%s::%s expects %s, exception %s, expected exception %s', DiscriminatorMapExtractor::class, 'extract', 'once', ClassExtractorException::class, ValidationException::class) => [
            'service' => 'classExtractor',
            'expects' => self::once(),
            'method' => 'extract',
            'exception' => new ClassExtractorException('request', TestConstant::EXCEPTION_MESSAGE),
            'expected_exception_class' => ValidationException::class,
            'parameters' => ['requestClass' => CreateSettingsRequest::class, 'argumentName' => 'request'],
        ];

        yield sprintf('%s::%s expects %s, exception %s, expected exception %s', DiscriminatorMapExtractor::class, 'extract', 'once', \TypeError::class, ConverterException::class) => [
            'service' => 'classExtractor',
            'expects' => self::once(),
            'method' => 'extract',
            'exception' => new \TypeError(TestConstant::TYPE_ERROR_EXCEPTION_MESSAGE),
            'expected_exception_class' => ValidationException::class,
            'parameters' => ['requestClass' => CreateSettingsRequest::class, 'argumentName' => 'request'],
        ];

        yield sprintf('%s::%s expects %s, exception %s, expected exception %s', Denormalizer::class, 'denormalize', 'once', \TypeError::class, ValidationException::class) => [
            'service' => 'denormalizer',
            'expects' => self::once(),
            'method' => 'denormalize',
            'exception' => new \TypeError(TestConstant::TYPE_ERROR_EXCEPTION_MESSAGE),
            'expected_exception_class' => ValidationException::class,
            'parameters' => ['requestClass' => CreateSettingsRequest::class, 'argumentName' => 'request'],
        ];

        yield sprintf('%s::%s expects %s, exception %s, expected exception %s', Denormalizer::class, 'denormalize', 'once', LogicException::class, ConverterException::class) => [
            'service' => 'denormalizer',
            'expects' => self::once(),
            'method' => 'denormalize',
            'exception' => new LogicException(TestConstant::EXCEPTION_MESSAGE),
            'expected_exception_class' => ConverterException::class,
            'parameters' => ['requestClass' => CreateSettingsRequest::class, 'argumentName' => 'request'],
        ];

        yield sprintf('%s::%s expects %s, exception %s, expected exception %s', Validator::class, 'validate', 'once', ConstraintException::class, ValidationException::class) => [
            'service' => 'validator',
            'expects' => self::once(),
            'method' => 'validate',
            'exception' => ConstraintException::fromConstraintViolationList(new ConstraintViolationList()),
            'expected_exception_class' => ValidationException::class,
            'parameters' => ['requestClass' => CreateSettingsRequest::class, 'argumentName' => 'request'],
        ];

        yield sprintf('%s::%s expects %s, exception %s, expected exception %s', RequestMapper::class, 'convert', 'once', ConverterException::class, ConverterException::class) => [
            'service' => 'converter',
            'expects' => self::once(),
            'method' => 'convert',
            'exception' => ConverterException::nullableParameterClass(),
            'expected_exception_class' => ConverterException::class,
            'parameters' => [],
            'will_service_throw' => false,
        ];
    }
}
