<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Converter;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Attribute\RequestBody;
use FRZB\Component\RequestMapper\Data\ValidationError;
use FRZB\Component\RequestMapper\Exception\ClassExtractorException;
use FRZB\Component\RequestMapper\Exception\ConstraintException;
use FRZB\Component\RequestMapper\Exception\ConverterException;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use FRZB\Component\RequestMapper\Extractor\ConstraintExtractor;
use FRZB\Component\RequestMapper\Extractor\DiscriminatorMapExtractor;
use FRZB\Component\RequestMapper\Extractor\ParametersExtractor;
use FRZB\Component\RequestMapper\Parser\ExceptionConverterInterface as ExceptionConverter;
use Symfony\Component\HttpFoundation\Request;
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

    public function convert(Request $request, RequestBody $attribute): object
    {
        $requestPayload = $request->request->all();
        $requestClass = $attribute->requestClass ?? throw ConverterException::nullableParameterClass();

        try {
            $targetClass = $this->classExtractor->extract($requestClass, $requestPayload);

            if ($attribute->isValidationNeeded) {
                $requestPayload = $this->parametersExtractor->extract($requestClass, $requestPayload);
                $constraints = $this->constraintExtractor->extract($targetClass, $requestPayload);

                $this->validate($requestPayload, $attribute->validationGroups, $constraints);
            }

            return $this->denormalizer->denormalize($requestPayload, $targetClass, self::DENORMALIZE_TYPE, $attribute->serializerContext);
        } catch (ClassExtractorException $e) {
            throw ValidationException::fromErrors(ValidationError::fromTypeAndClassExtractorException(DiscriminatorMap::class, $e));
        } catch (ConstraintException $e) {
            throw ValidationException::fromConstraintViolationList($e->getViolations());
        } catch (\TypeError $e) {
            throw ValidationException::fromErrors($this->exceptionConverter->convert($e, $requestPayload));
        } catch (\Throwable $e) {
            throw ConverterException::fromThrowable($e);
        }
    }

    /** @throws ValidationException */
    private function validate(mixed $target, array $validationGroups, ?Collection $constraints = null): void
    {
        if (($violations = $this->validator->validate($target, $constraints, $validationGroups))->count()) {
            throw ConstraintException::fromConstraintViolationList($violations);
        }
    }
}
