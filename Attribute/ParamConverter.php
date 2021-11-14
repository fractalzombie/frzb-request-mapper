<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Attribute;

use Attribute;
use FRZB\Component\RequestMapper\Data\ConverterType;
use Symfony\Component\HttpFoundation\Request;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION)]
final class ParamConverter
{
    public const ALLOWED_HTTP_METHODS = [Request::METHOD_GET, Request::METHOD_PUT, Request::METHOD_POST, Request::METHOD_PATCH, Request::METHOD_DELETE];
    public const ALLOWED_TYPES = [ConverterType::QUERY, ConverterType::REQUEST, ConverterType::ATTRIBUTE];
    public const ALLOWED_CONTENT_TYPES = ['application/json'];
    private const EXCEPTION_MESSAGE_TEMPLATE = 'Property "type" in class "%s" has not valid value "%s", allowed "%s"';

    /**
     * ParamConverter constructor.
     *
     * @param class-string $class
     */
    public function __construct(
        private string $name,
        private string $type,
        private string $class,
        private bool $isValidationNeeded = true,
        private array $context = [],
        private array $validationGroups = []
    ) {
        $this->throwIfTypeNotAllowed($type);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /** @return class-string */
    public function getClass(): string
    {
        return $this->class;
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

    private function throwIfTypeNotAllowed(string $type): void
    {
        if (\in_array($type, self::ALLOWED_TYPES, true)) {
            return;
        }

        $types = implode(', ', self::ALLOWED_TYPES);
        $message = sprintf(self::EXCEPTION_MESSAGE_TEMPLATE, self::class, $type, $types);

        throw new \InvalidArgumentException($message);
    }
}
