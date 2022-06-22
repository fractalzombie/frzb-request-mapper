<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Converter;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Data\ConverterData;
use FRZB\Component\RequestMapper\Data\ValidationError;
use FRZB\Component\RequestMapper\Exception\ClassExtractorException;
use FRZB\Component\RequestMapper\Exception\ConverterException;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use FRZB\Component\RequestMapper\Extractor\ConstraintExtractor;
use FRZB\Component\RequestMapper\Extractor\DiscriminatorMapExtractor;
use FRZB\Component\RequestMapper\Extractor\ParametersExtractor;
use FRZB\Component\RequestMapper\Parser\ExceptionConverterInterface as ExceptionConverter;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface as Denormalizer;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface as Validator;

#[AsService]
class RequestConverter implements ConverterInterface
{
    private const DENORMALIZE_TYPE = 'array';

    public function __construct(
        private readonly Validator $validator,
        private readonly Denormalizer $denormalizer,
        private readonly ExceptionConverter $exceptionConverter,
        private readonly DiscriminatorMapExtractor $classExtractor,
        private readonly ConstraintExtractor $constraintExtractor,
        private readonly ParametersExtractor $parametersExtractor,
    ) {
    }

    public function convert(ConverterData $data): object
    {
        $parameters = $data->getRequestParameters();
        $parameterClass = $data->getParameterClass() ?? throw ConverterException::nullableParameterClass();

        try {
            $class = $this->classExtractor->extract($parameterClass, $parameters);
        } catch (ClassExtractorException $e) {
            throw ValidationException::fromErrors(ValidationError::fromTypeAndClassExtractorException(DiscriminatorMap::class, $e));
        } catch (\Throwable $e) {
            throw ConverterException::fromThrowable($e);
        }

        if ($data->isValidationNeeded()) {
            try {
                $parameters = $this->parametersExtractor->extract($parameterClass, $parameters);
                $constraints = $this->constraintExtractor->extract($class, $parameters);

                $this->validate($parameters, $data->getValidationGroups(), $constraints);
            } catch (\TypeError $e) {
                throw ValidationException::fromErrors($this->exceptionConverter->convert($e, $parameters));
            }
        }

        try {
            $object = $this->denormalizer->denormalize($parameters, $class, self::DENORMALIZE_TYPE, $data->getSerializerContext());
        } catch (\TypeError $e) {
            throw ValidationException::fromErrors($this->exceptionConverter->convert($e, $parameters));
        } catch (\Throwable $e) {
            throw ConverterException::fromThrowable($e);
        }

        return $object;
    }

    /** @throws ValidationException */
    private function validate(mixed $target, array $validationGroups, ?Collection $constraints = null): void
    {
        if (($violations = $this->validator->validate($target, $constraints, $validationGroups))->count()) {
            throw ValidationException::fromConstraintViolationList($violations);
        }
    }
}
