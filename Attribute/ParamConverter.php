<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
final class ParamConverter
{
    /** @param class-string $class */
    public function __construct(
        private string $class,
        private ?string $name = null,
        private bool $isValidationNeeded = true,
        private array $context = [],
        private array $validationGroups = []
    ) {
    }

    /** @return class-string */
    public function getClass(): string
    {
        return $this->class;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isValidationNeeded(): bool
    {
        return $this->isValidationNeeded;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getValidationGroups(): array
    {
        return $this->validationGroups;
    }
}
