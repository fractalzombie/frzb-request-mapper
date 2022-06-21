<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

#[Immutable]
final class ConverterData
{
    private const DEFAULT_CONTEXT = [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true];

    public function __construct(
        private readonly Request $request,
        private readonly ParamConverter $attribute,
    ) {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getRequestParameters(): array
    {
        return $this->request->request->all();
    }

    #[Pure]
    public function getParameterClass(): ?string
    {
        return $this->attribute->getParameterClass();
    }

    #[Pure]
    public function isValidationNeeded(): bool
    {
        return $this->attribute->isValidationNeeded();
    }

    #[Pure]
    public function getSerializerContext(): array
    {
        return array_merge($this->attribute->getSerializerContext(), self::DEFAULT_CONTEXT);
    }

    #[Pure]
    public function getValidationGroups(): array
    {
        return $this->attribute->getValidationGroups();
    }
}
