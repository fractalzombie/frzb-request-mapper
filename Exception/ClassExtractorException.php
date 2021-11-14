<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;
use Throwable;

class ClassExtractorException extends \RuntimeException
{
    private string $property;

    #[Pure]
    public function __construct(string $property, string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->property = $property;
    }

    #[Pure]
    public static function createWhenParameterIsNull(DiscriminatorMap $discriminatorMap): self
    {
        $property = $discriminatorMap->getTypeProperty();
        $message = sprintf('%s cannot be null or empty', $property);

        return new self($property, $message);
    }

    #[Pure]
    public static function createWhenParameterInvalid(DiscriminatorMap $discriminatorMap): self
    {
        $property = $discriminatorMap->getTypeProperty();
        $expectedValues = array_keys($discriminatorMap->getMapping());
        $message = sprintf('%s can only be %s', $property, implode(', ', $expectedValues));

        return new self($message, $property);
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}
