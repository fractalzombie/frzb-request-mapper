<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

final class ConverterData
{
    private const DEFAULT_VALIDATION_GROUP = 'Default';
    private const DEFAULT_CONTEXT = [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true];

    private Request $request;
    private ParamConverter $attribute;

    public function __construct(Request $request, ParamConverter $attribute)
    {
        $this->request = $request;
        $this->attribute = $attribute;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return class-string
     */
    #[Pure]
    public function getClass(): string
    {
        return $this->attribute->getClass();
    }

    #[Pure]
    public function getConverterType(): string
    {
        return $this->attribute->getType();
    }

    #[Pure]
    public function getParameterName(): string
    {
        return $this->attribute->getName();
    }

    #[Pure]
    public function isValidationNeeded(): bool
    {
        return $this->attribute->isValidationNeeded();
    }

    #[Pure]
    public function getContext(): array
    {
        return array_merge($this->attribute->getContext(), self::DEFAULT_CONTEXT);
    }

    #[Pure]
    public function getValidationGroups(): array
    {
        return $this->attribute->getValidationGroups() ?: [self::DEFAULT_VALIDATION_GROUP];
    }
}
