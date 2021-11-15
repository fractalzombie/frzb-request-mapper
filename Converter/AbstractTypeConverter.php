<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Converter;

use FRZB\Component\RequestMapper\Data\ConverterData;
use FRZB\Component\RequestMapper\Data\Error;
use FRZB\Component\RequestMapper\Exception\ClassExtractorException;
use FRZB\Component\RequestMapper\Exception\TypeConverterException;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use FRZB\Component\RequestMapper\Extractor\ConstraintExtractor;
use FRZB\Component\RequestMapper\Extractor\DiscriminatorMapExtractor;
use FRZB\Component\RequestMapper\Extractor\ParametersExtractor;
use FRZB\Component\RequestMapper\Parser\ExceptionConverterInterface as ExceptionConverter;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface as Denormalizer;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface as Validator;

abstract class AbstractTypeConverter implements TypeConverter
{
    private const DENORMALIZE_TYPE = 'array';

    public function __construct(
        private Validator $validator,
        private Denormalizer $denormalizer,
        private ExceptionConverter $exceptionConverter,
        private DiscriminatorMapExtractor $classExtractor,
        private ConstraintExtractor $constraintExtractor,
        private ParametersExtractor $parametersMerge,
    ) {
    }

    /** @throws ValidationException */
    public function convert(ConverterData $data): object
    {
        try {
            $parameters = $this->getContent($data);
            $class = $this->classExtractor->extract($data->getClass(), $parameters);
        } catch (ClassExtractorException $e) {
            throw ValidationException::fromErrors(
                new Error(DiscriminatorMap::class, $e->getProperty(), $e->getMessage())
            );
        }

        if ($data->isValidationNeeded()) {
            $constraints = $this->constraintExtractor->extract($class);
            $parameters = $this->parametersMerge->extract($constraints?->fields ?? [], $parameters);

            $this->validate($parameters, $data->getValidationGroups(), $constraints);
        }

        try {
            $object = $this->denormalizer->denormalize($parameters, $class, self::DENORMALIZE_TYPE, $data->getContext());
        } catch (\TypeError $e) {
            $error = $this->exceptionConverter->convert($e, $parameters);

            throw ValidationException::fromErrors($error);
        } catch (\Throwable $e) {
            throw new TypeConverterException($e->getMessage(), (int) $e->getCode(), $e);
        }

        return $object;
    }

    /** Get content from Converter Data for deserialization. */
    abstract protected function getContent(ConverterData $data): array;

    /** @throws ValidationException */
    private function validate(mixed $target, array $validationGroups, ?Collection $constraints = null): void
    {
        if (($violations = $this->validator->validate($target, $constraints, $validationGroups))->count()) {
            throw ValidationException::fromConstrainViolationList($violations);
        }
    }
}
