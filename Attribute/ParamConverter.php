<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Attribute;

use JetBrains\PhpStorm\Pure;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
final class ParamConverter
{
    /** @param null|class-string $parameterClass */
    public function __construct(
        private ?string $parameterClass = null,
        private ?string $parameterName = null,
        private bool $isValidationNeeded = true,
        private array $serializerContext = [],
        private array $validationGroups = []
    ) {
    }

    /** @return null|class-string */
    public function getParameterClass(): ?string
    {
        return $this->parameterClass;
    }

    public function getParameterName(): ?string
    {
        return $this->parameterName;
    }

    public function isValidationNeeded(): bool
    {
        return $this->isValidationNeeded;
    }

    public function getSerializerContext(): array
    {
        return $this->serializerContext;
    }

    public function getValidationGroups(): array
    {
        return $this->validationGroups;
    }

    #[Pure]
    public function equals(object $object): bool
    {
        return match (true) {
            $object instanceof \ReflectionParameter => $object->getType()?->getName() === $this->parameterClass || $object->getName() === $this->parameterName,
            $object instanceof self => $object->getParameterClass() === $this->parameterClass || $object->getParameterName() === $this->parameterName,
            default => false
        };
    }
}
