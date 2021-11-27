<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;
use Throwable;

final class ClassExtractorException extends \RuntimeException
{
    #[Pure]
    public function __construct(private string $property, string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    #[Pure]
    public static function fromDiscriminatorMapWhenParameterIsNull(DiscriminatorMap $discriminatorMap): self
    {
        $property = $discriminatorMap->getTypeProperty();
        $message = sprintf('%s cannot be null or empty', $property);

        return new self($property, $message);
    }

    #[Pure]
    public static function fromDiscriminatorMapWhenParameterInvalid(DiscriminatorMap $discriminatorMap): self
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
